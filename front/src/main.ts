import app from '@/plugins/app'
import '@/plugins'
import '@/assets/css/app.css'
import '@/assets/main.css'
import 'vue3-toastify/dist/index.css';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'


/* import specific icons */
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

library.add(fas, far, fab);

app.component("font-awesome-icon", FontAwesomeIcon)

app.mount('#app')
