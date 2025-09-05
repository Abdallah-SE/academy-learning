'use client';

import { useEffect } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { useLanguage } from '@/context/LanguageContext';
import { useTranslations } from '@/hooks/useTranslations';
import { useRouter } from 'next/navigation';
import { LanguageSelector } from '@/components/atoms/LanguageSelector';
import { Loading } from '@/components/atoms/Loading';
import { MainLayout } from '@/components/organisms/MainLayout';

export default function AdminDashboardPage() {
  const { user, isAuthenticated, isInitializing, logout } = useAuthContext();
  const { currentLanguage } = useLanguage();
  const t = useTranslations('dashboard');
  const router = useRouter();

 

  useEffect(() => {
    if (!isInitializing && !isAuthenticated) { 
      router.push('/admin/login');
    }
  }, [isAuthenticated, isInitializing, router]);

  if (isInitializing) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <Loading />
      </div>
    );
  }

  if (!isAuthenticated) {
    return null;
  }

  return (
    <MainLayout>
      {/* Header */}
      <header className="bg-white border-b border-gray-200 shadow-sm">
        <div className="flex items-center justify-between h-16 px-6">
          <div className="flex items-center space-x-4">
            <h1 className="text-xl font-semibold text-gray-900">
              {t('title')}
            </h1>
          </div>
          
          <div className="flex items-center space-x-4">
            <LanguageSelector />
            <div className="h-6 w-px bg-gray-300"></div>
            <button
              onClick={logout}
              className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
            >
              {t('logout')}
            </button>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="flex-1 overflow-auto">
        <div className="max-w-7xl mx-auto py-8 px-6">
          {/* Welcome Section */}
          <div className="mb-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-2">
              {t('welcome', { name: user?.name || 'Admin' })}
            </h2>
            <p className="text-gray-600">
              Here's what's happening with your academy today
            </p>
          </div>

          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <span className="text-blue-600 text-xl">👥</span>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-600">{t('totalUsers')}</p>
                  <p className="text-2xl font-bold text-gray-900">1,234</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <span className="text-green-600 text-xl">📚</span>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-600">{t('activeCourses')}</p>
                  <p className="text-2xl font-bold text-gray-900">56</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <span className="text-yellow-600 text-xl">💰</span>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-600">{t('totalRevenue')}</p>
                  <p className="text-2xl font-bold text-gray-900">$45,678</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <span className="text-purple-600 text-xl">⭐</span>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-600">Rating</p>
                  <p className="text-2xl font-bold text-gray-900">4.8</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </MainLayout>
  );
}