import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { DocumentDetailPage } from '@/features/documents';
import { officeApi } from '@/api';
import type { OfficeDocument } from '@/types';

export function DocumentDetail() {
  const { id } = useParams<{ id: string }>();
  const [document, setDocument] = useState<OfficeDocument | undefined>();
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    setIsLoading(true);
    officeApi.syncDocuments().then((res) => {
      const doc = res.data.find((d) => d.id === Number(id));
      if (doc) {
        const updated = { ...doc, is_read: true };
        setDocument(updated);
      } else {
        setDocument(undefined);
      }
    }).catch(() => {
      setDocument(undefined);
    }).finally(() => {
      setIsLoading(false);
    });
  }, [id]);

  return <DocumentDetailPage document={document} isLoading={isLoading} />;
}
