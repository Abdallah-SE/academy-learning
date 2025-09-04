import { useLanguage } from '@/context/LanguageContext';

// Professional translation object
const translations = {
  en: {
    dashboard: {
      title: 'Dashboard',
      welcome: 'Welcome, {name}!',
      totalUsers: 'Total Users',
      activeCourses: 'Active Courses',
      totalRevenue: 'Total Revenue',
      logout: 'Logout',
      overview: 'Overview',
      statistics: 'Statistics',
      recentActivity: 'Recent Activity',
      quickActions: 'Quick Actions',
      settings: 'Settings',
      profile: 'Profile',
      help: 'Help'
    },
    common: {
      language: 'Language',
      loading: 'Loading...',
      error: 'Error',
      success: 'Success',
      cancel: 'Cancel',
      save: 'Save',
      delete: 'Delete',
      edit: 'Edit',
      view: 'View'
    },
    auth: {
      login: 'Login',
      logout: 'Logout',
      email: 'Email',
      password: 'Password',
      rememberMe: 'Remember me',
      forgotPassword: 'Forgot password?'
    },
    sidebar: {
      dashboard: 'Dashboard',
      users: 'Users',
      courses: 'Courses',
      revenue: 'Revenue',
      settings: 'Settings',
      analytics: 'Analytics',
      notifications: 'Notifications',
      help: 'Help'
    }
  },
  ar: {
    dashboard: {
      title: 'لوحة التحكم',
      welcome: 'مرحباً، {name}!',
      totalUsers: 'إجمالي المستخدمين',
      activeCourses: 'الدورات النشطة',
      totalRevenue: 'إجمالي الإيرادات',
      logout: 'تسجيل الخروج',
      overview: 'نظرة عامة',
      statistics: 'الإحصائيات',
      recentActivity: 'النشاط الأخير',
      quickActions: 'إجراءات سريعة',
      settings: 'الإعدادات',
      profile: 'الملف الشخصي',
      help: 'المساعدة'
    },
    common: {
      language: 'اللغة',
      loading: 'جاري التحميل...',
      error: 'خطأ',
      success: 'نجح',
      cancel: 'إلغاء',
      save: 'حفظ',
      delete: 'حذف',
      edit: 'تعديل',
      view: 'عرض'
    },
    auth: {
      login: 'تسجيل الدخول',
      logout: 'تسجيل الخروج',
      email: 'البريد الإلكتروني',
      password: 'كلمة المرور',
      rememberMe: 'تذكرني',
      forgotPassword: 'نسيت كلمة المرور؟'
    },
    sidebar: {
      dashboard: 'لوحة التحكم',
      users: 'المستخدمين',
      courses: 'الدورات',
      revenue: 'الإيرادات',
      settings: 'الإعدادات',
      analytics: 'التحليلات',
      notifications: 'الإشعارات',
      help: 'المساعدة'
    }
  },
  de: {
    dashboard: {
      title: 'Dashboard',
      welcome: 'Willkommen, {name}!',
      totalUsers: 'Gesamtbenutzer',
      activeCourses: 'Aktive Kurse',
      totalRevenue: 'Gesamtumsatz',
      logout: 'Abmelden',
      overview: 'Übersicht',
      statistics: 'Statistiken',
      recentActivity: 'Letzte Aktivität',
      quickActions: 'Schnellaktionen',
      settings: 'Einstellungen',
      profile: 'Profil',
      help: 'Hilfe'
    },
    common: {
      language: 'Sprache',
      loading: 'Laden...',
      error: 'Fehler',
      success: 'Erfolg',
      cancel: 'Abbrechen',
      save: 'Speichern',
      delete: 'Löschen',
      edit: 'Bearbeiten',
      view: 'Anzeigen'
    },
    auth: {
      login: 'Anmelden',
      logout: 'Abmelden',
      email: 'E-Mail',
      password: 'Passwort',
      rememberMe: 'Angemeldet bleiben',
      forgotPassword: 'Passwort vergessen?'
    },
    sidebar: {
      dashboard: 'Dashboard',
      users: 'Benutzer',
      courses: 'Kurse',
      revenue: 'Einnahmen',
      settings: 'Einstellungen',
      analytics: 'Analysen',
      notifications: 'Benachrichtigungen',
      help: 'Hilfe'
    }
  }
};

export const useTranslations = (namespace: keyof typeof translations.en) => {
  const { currentLanguage } = useLanguage();
  
  const t = (key: string, params?: Record<string, any>) => {
    const namespaceTranslations = translations[currentLanguage as keyof typeof translations]?.[namespace];
    let text = namespaceTranslations?.[key as keyof typeof namespaceTranslations] || key;
    
    if (params) {
      Object.keys(params).forEach(param => {
        text = text.replace(`{${param}}`, params[param]);
      });
    }
    
    return text;
  };
  
  return t;
};
