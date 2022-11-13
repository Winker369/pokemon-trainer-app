import { createApp } from 'vue'
import Pokemon from '@/components/Pokemon.vue'

const app = createApp({});

app.component('pokemon', Pokemon);
app.mount('#app');
