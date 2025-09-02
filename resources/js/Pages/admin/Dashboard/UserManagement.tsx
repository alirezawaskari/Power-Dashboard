import React from 'react';
import { Card, CardBody, CardHeader, Badge, Button, Dropdown } from 'react-bootstrap';
import TableContainer from '../../../Components/Common/TableContainer';
import FeatherIcon from "feather-icons-react";
import { useTranslation } from 'react-i18next';

const UserManagement = () => {
    const { t } = useTranslation();
    // Mock user data (replace with API call later)
    const users = [
        {
            id: 1,
            name: "John Smith",
            email: "john.smith@company.com",
            role: "admin",
            status: "active",
            devices: 8,
            lastLogin: t("2 hours ago")
        },
        {
            id: 2,
            name: "Sarah Johnson",
            email: "sarah.johnson@company.com",
            role: "operator",
            status: "active",
            devices: 5,
            lastLogin: t("1 hour ago")
        },
        {
            id: 3,
            name: "Mike Wilson",
            email: "mike.wilson@company.com",
            role: "viewer",
            status: "active",
            devices: 3,
            lastLogin: t("30 min ago")
        },
        {
            id: 4,
            name: "Lisa Brown",
            email: "lisa.brown@company.com",
            role: "support",
            status: "inactive",
            devices: 0,
            lastLogin: t("2 days ago")
        },
        {
            id: 5,
            name: "David Lee",
            email: "david.lee@company.com",
            role: "operator",
            status: "active",
            devices: 6,
            lastLogin: t("4 hours ago")
        }
    ];

    const getRoleColor = (role: string) => {
        switch (role) {
            case 'admin': return 'danger';
            case 'operator': return 'primary';
            case 'viewer': return 'info';
            case 'support': return 'warning';
            default: return 'secondary';
        }
    };

    const getStatusColor = (status: string) => {
        return status === 'active' ? 'success' : 'danger';
    };

    const columns = [
        {
            header: t("Name"),
            accessorKey: "name",
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                return (
                    <div>
                        <h6 className="mb-0">{cellProps.row.original.name}</h6>
                        <small className="text-muted">{cellProps.row.original.email}</small>
                    </div>
                );
            },
        },
        {
            header: t("Role"),
            accessorKey: "role",
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                const role = cellProps.row.original.role;
                const roleText = role === 'admin' ? t('Admin') :
                               role === 'operator' ? t('Operator') :
                               role === 'viewer' ? t('Viewer') :
                               role === 'support' ? t('Support') : role;
                return (
                    <Badge bg={getRoleColor(role)}>
                        {roleText}
                    </Badge>
                );
            },
        },
        {
            header: t("Status"),
            accessorKey: "status",
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                const status = cellProps.row.original.status;
                const statusText = status === 'active' ? t('Active') : t('Inactive');
                return (
                    <Badge bg={getStatusColor(status)}>
                        {statusText}
                    </Badge>
                );
            },
        },
        {
            header: t("Devices"),
            accessorKey: "devices",
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                return <span className="fw-semibold">{cellProps.row.original.devices}</span>;
            },
        },
        {
            header: t("Last Login"),
            accessorKey: "lastLogin",
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                return <span className="text-muted">{cellProps.row.original.lastLogin}</span>;
            },
        },
        {
            header: t("Actions"),
            enableColumnFilter: false,
            cell: (cellProps: any) => {
                return (
                    <Dropdown>
                        <Dropdown.Toggle variant="light" size="sm">
                            <FeatherIcon icon="more-horizontal" size={16} />
                        </Dropdown.Toggle>
                        <Dropdown.Menu>
                            <Dropdown.Item>
                                <FeatherIcon icon="edit" size={14} className="me-2" />
                                {t('Edit User')}
                            </Dropdown.Item>
                            <Dropdown.Item>
                                <FeatherIcon icon="shield" size={14} className="me-2" />
                                {t('Change Role')}
                            </Dropdown.Item>
                            <Dropdown.Item>
                                <FeatherIcon icon="key" size={14} className="me-2" />
                                {t('Reset Password')}
                            </Dropdown.Item>
                            <Dropdown.Divider />
                            <Dropdown.Item className="text-danger">
                                <FeatherIcon icon="trash-2" size={14} className="me-2" />
                                {t('Delete User')}
                            </Dropdown.Item>
                        </Dropdown.Menu>
                    </Dropdown>
                );
            },
        }
    ];

    return (
        <Card>
            <CardHeader className="d-flex justify-content-between align-items-center">
                <h4 className="card-title mb-0">{t('User Management')}</h4>
                <Button variant="primary" size="sm">
                    <FeatherIcon icon="plus" size={16} className="me-1" />
                    {t('Add User')}
                </Button>
            </CardHeader>
            <CardBody>
                <TableContainer
                    columns={columns}
                    data={users}
                    isGlobalFilter={true}
                    customPageSize={5}
                    className="custom-header-css"
                />
            </CardBody>
        </Card>
    );
};

export default UserManagement;
