import { usePage } from '@inertiajs/react';

export const useAuth = () => {
    const { props } = usePage();
    const user = props.auth?.user;
    
    return {
        user,
        isAuthenticated: !!user,
        role: user?.role || 'user',
        isAdmin: user?.role === 'admin' || user?.role === 'owner',
        isOwner: user?.role === 'owner',
        isOperator: user?.role === 'operator',
        isViewer: user?.role === 'viewer',
        isSupport: user?.role === 'support'
    };
};
