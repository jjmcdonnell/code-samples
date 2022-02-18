import { createApp } from 'vue';
import { createStore } from 'vuex';
import axios from 'axios';
import Api from './api/index.js';

import { NMessageProvider } from 'naive-ui';
import { NProgress } from 'naive-ui';
import NBasicCalendar  from './components/naive-ui/n-calendar.vue';

import MetaSelects  from './store/modules/meta-selects/module.js';
import MetaSelectList from './components/meta/select-list.vue';

const store = createStore({
    modules:{
        MetaSelects,
    }
});

const app = createApp({
    delimiters: ['{!{', '}!}'],
    components:{
        'message-area': NMessageProvider,
        'calendar': NBasicCalendar,
        'progress-circle': NProgress,
        'meta-select': MetaSelectList,
    }
}).use(store).mount('#app-main');