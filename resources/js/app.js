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

const termAddedBanner = document.getElementById("term-added-banner");
if (termAddedBanner) {
  import("canvas-confetti")
    .then(({ default: confetti }) => {
      const allTermsCount = +termAddedBanner.dataset.allTermsCount;

      return confetti({
        disableForReducedMotion: true,
        particleCount: allTermsCount === 10 ? 50 : 200,
        spread: allTermsCount === 10 ? 45 : 60,
      });
    })
    .catch(() => {
      // noop
    });
}
