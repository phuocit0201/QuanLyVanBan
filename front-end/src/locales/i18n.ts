import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import vi from './vi.json';
import en from './en.json';

const savedLocale = localStorage.getItem('ui-storage');
let defaultLocale = 'vi';
try {
  if (savedLocale) {
    const parsed = JSON.parse(savedLocale);
    if (parsed.state?.locale) {
      defaultLocale = parsed.state.locale;
    }
  }
} catch {}

i18n.use(initReactI18next).init({
  resources: {
    vi: { translation: vi },
    en: { translation: en },
  },
  lng: defaultLocale,
  fallbackLng: 'vi',
  interpolation: {
    escapeValue: false,
  },
});

export default i18n;
