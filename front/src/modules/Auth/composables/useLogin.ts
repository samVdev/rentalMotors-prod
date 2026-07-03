import { ref } from "vue"
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/modules/Auth/stores'
import * as AuthService from "@/modules/Auth/services";
import type { FormLogin} from '@/modules/Auth/types/Auth'
import { alertWithToast } from "@/utils/toast";

export function useLogin() {
  const router = useRouter();
  const auth = useAuthStore()
  const sending = ref(false)

  const login = async (form: FormLogin) => {
    const payload = {
      username: form.user,
      password: form.password,
    }
    try {
      sending.value = true;
     await AuthService.login(payload);
      const authUser = await auth.getAuthUser();
      if (authUser) {
        auth.setGuest({ value: "isNotGuest" });
        if(authUser.isAdmin) {
          const response = await AuthService.getAuthMenu()
          const firstMenu = response.data[0];
          await router.push(firstMenu.path);
        }
        else await router.push("/home");
      }
    } catch (err) {
      let message = "Ha ocurrido un error inesperado.";

      if (err?.response?.data?.errors) {
          const errors = err.response.data.errors;
          const firstKey = Object.keys(errors)[0];
          message = errors[firstKey][0] || message;
      }
      
      else if (err?.response?.data?.message) {
          message = err.response.data.message;
      }
      else if (err?.message) {
          message = err.message;
      }
      
      alertWithToast(message, "error");
      

    } finally {
      sending.value = false;
    }
  }

  return {
    login,
    sending,
  }
}
