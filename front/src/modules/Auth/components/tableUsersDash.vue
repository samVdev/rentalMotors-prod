<script lang="ts" setup>
const props = defineProps<{
    users: any[]
}>()

</script>

<template>
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <tbody>
            <tr class="border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" v-for="user in users">
                <th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
                    <img class="w-10 h-10 rounded-full" src="https://static.vecteezy.com/system/resources/previews/009/292/244/original/default-avatar-icon-of-social-media-user-vector.jpg" alt="Jese image">
                    <div class="ps-3">
                        <div class="text-base font-semibold">{{user.fullName}}</div>
                        <div class="font-normal text-gray-500">{{user.email}}</div>
                    </div>  
                </th>
            </tr>
        </tbody> 
    </table>
</template>