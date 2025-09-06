'use client';

import React, { useState } from 'react';

export const ApiDebugger: React.FC = () => {
  const [debugInfo, setDebugInfo] = useState<any>(null);
  const [loading, setLoading] = useState(false);

  const testApiConnection = async () => {
    setLoading(true);
    try {
      // Import the admin service to test with proper authentication
      const { adminService } = await import('@/services/adminService');
      
      const data = await adminService.getAdmins();
      
      setDebugInfo({
        status: 'Success',
        data: data,
        auth: 'Using HttpOnly cookies',
      });
    } catch (error) {
      setDebugInfo({
        error: error instanceof Error ? error.message : 'Unknown error',
        auth: 'Using HttpOnly cookies',
      });
    } finally {
      setLoading(false);
    }
  };

  const testWithoutAuth = async () => {
    setLoading(true);
    try {
      const response = await fetch('http://localhost:8000/api/v1/admin/admins', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
      });

      const data = await response.json();
      
      setDebugInfo({
        status: response.status,
        statusText: response.statusText,
        headers: Object.fromEntries(response.headers.entries()),
        data: data,
        test: 'Without Auth',
      });
    } catch (error) {
      setDebugInfo({
        error: error instanceof Error ? error.message : 'Unknown error',
        test: 'Without Auth',
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="p-4 bg-gray-100 rounded-lg">
      <h3 className="text-lg font-semibold mb-4">API Debugger</h3>
      
      <div className="space-x-2 mb-4">
        <button
          onClick={testApiConnection}
          disabled={loading}
          className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
        >
          {loading ? 'Testing...' : 'Test with Auth'}
        </button>
        
        <button
          onClick={testWithoutAuth}
          disabled={loading}
          className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 disabled:opacity-50"
        >
          {loading ? 'Testing...' : 'Test without Auth'}
        </button>
      </div>

      {debugInfo && (
        <div className="mt-4">
          <h4 className="font-semibold mb-2">Debug Results:</h4>
          <pre className="bg-gray-800 text-green-400 p-4 rounded overflow-auto text-sm">
            {JSON.stringify(debugInfo, null, 2)}
          </pre>
        </div>
      )}
    </div>
  );
};
