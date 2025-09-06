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
    },
    admin: {
      title: 'Admin Management',
      admins: 'Admins',
      addAdmin: 'Add Admin',
      searchPlaceholder: 'Search by name, email, or username...',
      allStatus: 'All Status',
      allRoles: 'All Roles',
      active: 'Active',
      inactive: 'Inactive',
      suspended: 'Suspended',
      superAdmin: 'Super Admin',
      admin: 'Admin',
      moderator: 'Moderator',
      avatar: 'Avatar',
      name: 'Name',
      username: 'Username',
      status: 'Status',
      lastLogin: 'Last Login',
      created: 'Created',
      actions: 'Actions',
      viewDetails: 'View Details',
      editAdmin: 'Edit Admin',
      deleteAdmin: 'Delete Admin',
      never: 'Never',
      justNow: 'Just now',
      hoursAgo: '{hours}h ago',
      daysAgo: '{days}d ago',
      logout: 'Logout',
      refresh: 'Refresh',
      export: 'Export'
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
    },
    admin: {
      title: 'إدارة المدراء',
      admins: 'المدراء',
      addAdmin: 'إضافة مدير',
      searchPlaceholder: 'البحث بالاسم أو البريد الإلكتروني أو اسم المستخدم...',
      allStatus: 'جميع الحالات',
      allRoles: 'جميع الأدوار',
      active: 'نشط',
      inactive: 'غير نشط',
      suspended: 'معلق',
      superAdmin: 'مدير عام',
      admin: 'مدير',
      moderator: 'مشرف',
      avatar: 'الصورة الشخصية',
      name: 'الاسم',
      username: 'اسم المستخدم',
      status: 'الحالة',
      lastLogin: 'آخر تسجيل دخول',
      created: 'تاريخ الإنشاء',
      actions: 'الإجراءات',
      viewDetails: 'عرض التفاصيل',
      editAdmin: 'تعديل المدير',
      deleteAdmin: 'حذف المدير',
      never: 'أبداً',
      justNow: 'الآن',
      hoursAgo: 'منذ {hours} ساعة',
      daysAgo: 'منذ {days} يوم',
      logout: 'تسجيل الخروج',
      refresh: 'تحديث',
      export: 'تصدير'
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
    },
    admin: {
      title: 'Admin-Verwaltung',
      admins: 'Admins',
      addAdmin: 'Admin hinzufügen',
      searchPlaceholder: 'Nach Name, E-Mail oder Benutzername suchen...',
      allStatus: 'Alle Status',
      allRoles: 'Alle Rollen',
      active: 'Aktiv',
      inactive: 'Inaktiv',
      suspended: 'Gesperrt',
      superAdmin: 'Super Admin',
      admin: 'Admin',
      moderator: 'Moderator',
      avatar: 'Avatar',
      name: 'Name',
      username: 'Benutzername',
      status: 'Status',
      lastLogin: 'Letzter Login',
      created: 'Erstellt',
      actions: 'Aktionen',
      viewDetails: 'Details anzeigen',
      editAdmin: 'Admin bearbeiten',
      deleteAdmin: 'Admin löschen',
      never: 'Nie',
      justNow: 'Gerade eben',
      hoursAgo: 'vor {hours}h',
      daysAgo: 'vor {days}d',
      logout: 'Abmelden',
      refresh: 'Aktualisieren',
      export: 'Exportieren'
    }
  }
};

export const useTranslations = (namespace: keyof typeof translations.en) => {
  const { currentLanguage } = useLanguage();
  
  const t = (key: string, params?: Record<string, string | number>) => {
    const namespaceTranslations = translations[currentLanguage as keyof typeof translations]?.[namespace];
    let text: string = (namespaceTranslations as Record<string, string>)?.[key] || key;
    
    if (params) {
      Object.keys(params).forEach(param => {
        text = text.replace(`{${param}}`, String(params[param]));
      });
    }
    
    return text;
  };
  
  return t;
};
