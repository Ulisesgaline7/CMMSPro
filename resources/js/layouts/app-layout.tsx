import CmmsLayout from '@/layouts/cmms-layout';
import type { AppLayoutProps } from '@/types';

export default ({ children, ...props }: AppLayoutProps) => (
    <CmmsLayout {...props}>
        {children}
    </CmmsLayout>
);
