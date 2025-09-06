'use client';

import React, { useState, useEffect } from 'react';
import { useAuthContext } from '@/context/AuthContext';
import { adminService } from '@/services/adminService';

export default function TestAuthPage() {
  const { user, isAuthenticated, isInitializing } = useAuthContext();
  const [testResult, setTestResult] = useState<any>(null);
  const [loading, setLoading] = useState(false);

  const testAdminApi = async () => {
    setLoading(true);
    try {
      // First test the debug endpoint
      const debugResponse = await fetch('http://localhost:8000/debug/auth');
      const debugData = await debugResponse.json();
      
      // Then test the admin API
      const result = await adminService.getAdmins();
      setTestResult({
        success: true,
        data: result,
        debug: debugData,
        message: 'API call successful'
      });
    } catch (error) {
      setTestResult({
        success: false,
        error: error instanceof Error ? error.message : 'Unknown error',
        message: 'API call failed'
      });
    } finally {
      setLoading(false);
    }
  };

  if (isInitializing) {
    return <div>Loading...</div>;
  }

  return (
    <div className="p-8">
      <h1 className="text-2xl font-bold mb-4">Authentication Test</h1>
      
      <div className="mb-4">
        <h2 className="text-lg font-semibold mb-2">Auth Status:</h2>
        <p>Authenticated: {isAuthenticated ? 'Yes' : 'No'}</p>
        <p>User: {user ? user.name : 'None'}</p>
        <p>Email: {user ? user.email : 'None'}</p>
      </div>

      <div className="mb-4">
        <button
          onClick={testAdminApi}
          disabled={loading}
          className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
        >
          {loading ? 'Testing...' : 'Test Admin API'}
        </button>
      </div>

      {testResult && (
        <div className="mt-4">
          <h3 className="text-lg font-semibold mb-2">Test Result:</h3>
          <div className={`p-4 rounded ${testResult.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
            <p><strong>Status:</strong> {testResult.success ? 'Success' : 'Failed'}</p>
            <p><strong>Message:</strong> {testResult.message}</p>
            {testResult.error && <p><strong>Error:</strong> {testResult.error}</p>}
          </div>
          
          {testResult.data && (
            <div className="mt-4">
              <h4 className="font-semibold mb-2">Response Data:</h4>
              <pre className="bg-gray-100 p-4 rounded overflow-auto text-sm">
                {JSON.stringify(testResult.data, null, 2)}
              </pre>
            </div>
          )}
        </div>
      )}
    </div>
  );
}
