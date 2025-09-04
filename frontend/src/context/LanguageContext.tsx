'use client';

import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';

interface LanguageContextType {
  currentLanguage: string;
  changeLanguage: (lang: string) => Promise<void>;
}

const LanguageContext = createContext<LanguageContextType | undefined>(undefined);

export const LanguageProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [currentLanguage, setCurrentLanguage] = useState('en');

  useEffect(() => {
    const savedLang = localStorage.getItem('preferredLanguage') || 'en';
    setCurrentLanguage(savedLang);
    updateDocumentAttributes(savedLang);
  }, []);

  const updateDocumentAttributes = (lang: string) => {
    document.documentElement.lang = lang;
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
  };

  const changeLanguage = async (lang: string) => {
    // Update frontend ONLY - no backend calls
    setCurrentLanguage(lang);
    localStorage.setItem('preferredLanguage', lang);
    updateDocumentAttributes(lang);
    
    // Optional: You can add backend call here later if needed
    // try {
    //   await fetch('/api/v1/set-locale', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify({ locale: lang })
    //   });
    // } catch (error) {
    //   console.error('Backend locale update failed:', error);
    // }
  };

  return (
    <LanguageContext.Provider value={{ currentLanguage, changeLanguage }}>
      {children}
    </LanguageContext.Provider>
  );
};

export const useLanguage = () => {
  const context = useContext(LanguageContext);
  if (!context) {
    throw new Error('useLanguage must be used within LanguageProvider');
  }
  return context;
};