import "@fontsource-variable/work-sans";
import "@github/details-menu-element";
import Alpine from "alpinejs";
import axios from "axios";
import { createIcons, ChevronDown, X } from "lucide";

window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

createIcons({
  icons: {
    ChevronDown,
    X,
  },
});

window.Alpine = Alpine;
Alpine.start();
