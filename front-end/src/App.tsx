import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ToastProvider } from '@/components/ui/Toast';
import { PublicRoute, ProtectedRoute } from '@/components/routes';
import { AppLayout } from '@/components/layout';
import { LoginPage, RegisterPage } from '@/features/auth';
import { HomePage } from '@/features/home';
import { DocumentListPage, DocumentDetail } from '@/features/documents';
import { ProfilePage } from '@/features/profile';
import { useEffect, useState } from 'react';
import { officeApi } from '@/api';
import type { OfficeDocument } from '@/types';

const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      retry: 1,
      refetchOnWindowFocus: false,
    },
  },
});

function DocumentsPage() {
  const [documents, setDocuments] = useState<OfficeDocument[]>([]);
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    setIsLoading(true);
    officeApi.syncDocuments()
      .then((res) => setDocuments(res.data))
      .catch(() => setDocuments([]))
      .finally(() => setIsLoading(false));
  }, []);

  return <DocumentListPage documents={documents} isLoading={isLoading} />;
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <ToastProvider>
        <BrowserRouter>
          <Routes>
            {/* Public routes */}
            <Route element={<PublicRoute />}>
              <Route path="/login" element={<LoginPage />} />
              <Route path="/register" element={<RegisterPage />} />
            </Route>

            {/* Protected routes */}
            <Route element={<ProtectedRoute />}>
              <Route element={<AppLayout />}>
                <Route path="/" element={<HomePage />} />
                <Route path="/documents" element={<DocumentsPage />} />
                <Route path="/documents/:id" element={<DocumentDetail />} />
                <Route path="/profile" element={<ProfilePage />} />
              </Route>
            </Route>
          </Routes>
        </BrowserRouter>
      </ToastProvider>
    </QueryClientProvider>
  );
}

export default App;
