import { createApp } from "vue";
import App from "./App.vue";
import PrimeVue from "primevue/config";
import router from "./router";
import "primevue/resources/themes/lara-light-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import "./assets/main.css";
import AutoComplete from "primevue/autocomplete";

const app = createApp(App);
app.component("AutoComplete", AutoComplete);
app.use(PrimeVue);
app.use(router);
app.mount("#app");
