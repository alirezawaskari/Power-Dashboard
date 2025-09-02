import React, { useState } from 'react';
import { Card, Col, Container, Row, Badge, Table, Button, Dropdown, Form, InputGroup, Modal } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import FeatherIcon from 'feather-icons-react';
import { useTranslation } from 'react-i18next';

const UserManagement = () => {
    const { t } = useTranslation();
    const [users, setUsers] = useState([
        {
            id: 1,
            name: 'John Doe',
            email: 'john.doe@example.com',
            role: 'admin',
            status: 'active',
            lastLogin: '2024-01-15 10:30',
            devices: 5,
            avatar: '/images/users/avatar-1.jpg'
        },
        {
            id: 2,
            name: 'Jane Smith',
            email: 'jane.smith@example.com',
            role: 'user',
            status: 'active',
            lastLogin: '2024-01-15 09:15',
            devices: 3,
            avatar: '/images/users/avatar-2.jpg'
        },
        {
            id: 3,
            name: 'Mike Johnson',
            email: 'mike.johnson@example.com',
            role: 'user',
            status: 'inactive',
            lastLogin: '2024-01-14 16:45',
            devices: 2,
            avatar: '/images/users/avatar-3.jpg'
        },
        {
            id: 4,
            name: 'Sarah Wilson',
            email: 'sarah.wilson@example.com',
            role: 'admin',
            status: 'active',
            lastLogin: '2024-01-15 11:20',
            devices: 8,
            avatar: '/images/users/avatar-4.jpg'
        }
    ]);

    const [showAddModal, setShowAddModal] = useState(false);
    const [newUser, setNewUser] = useState({
        name: '',
        email: '',
        role: 'user',
        status: 'active'
    });

    const getRoleBadge = (role: string) => {
        switch (role) {
            case 'admin':
                return <Badge bg="danger">Admin</Badge>;
            case 'user':
                return <Badge bg="primary">User</Badge>;
            default:
                return <Badge bg="secondary">Unknown</Badge>;
        }
    };

    const getStatusBadge = (status: string) => {
        switch (status) {
            case 'active':
                return <Badge bg="success">Active</Badge>;
            case 'inactive':
                return <Badge bg="secondary">Inactive</Badge>;
            default:
                return <Badge bg="warning">Pending</Badge>;
        }
    };

    const handleAddUser = () => {
        const user = {
            id: users.length + 1,
            ...newUser,
            lastLogin: 'Never',
            devices: 0,
            avatar: '/images/users/avatar-default.jpg'
        };
        setUsers([...users, user]);
        setShowAddModal(false);
        setNewUser({ name: '', email: '', role: 'user', status: 'active' });
    };

    return (
        <React.Fragment>
            <Head title={t('User Management') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('User Management')} pageTitle={t('Users')} />
                    
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <Card.Header>
                                    <div className="d-flex align-items-center">
                                        <h5 className="card-title mb-0 flex-grow-1">{t('System Users')}</h5>
                                        <div className="flex-shrink-0">
                                            <Button variant="primary" size="sm" onClick={() => setShowAddModal(true)}>
                                                <FeatherIcon icon="plus" className="icon-sm me-1" />
                                                {t('Add User')}
                                            </Button>
                                        </div>
                                    </div>
                                </Card.Header>
                                <Card.Body>
                                    <div className="row mb-3">
                                        <div className="col-md-6">
                                            <InputGroup>
                                                <InputGroup.Text>
                                                    <FeatherIcon icon="search" className="icon-sm" />
                                                </InputGroup.Text>
                                                <Form.Control placeholder={t('Search users')} />
                                            </InputGroup>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Select>
                                                <option>{t('All Roles')}</option>
                                                <option>{t('Admin')}</option>
                                                <option>{t('User')}</option>
                                            </Form.Select>
                                        </div>
                                        <div className="col-md-3">
                                            <Form.Select>
                                                <option>{t('All Status')}</option>
                                                <option>{t('Active')}</option>
                                                <option>{t('Inactive')}</option>
                                            </Form.Select>
                                        </div>
                                    </div>
                                    
                                    <div className="table-responsive">
                                        <Table className="table-borderless table-nowrap align-middle mb-0">
                                            <thead className="table-light">
                                                <tr>
                                                    <th scope="col">{t('User')}</th>
                                                    <th scope="col">{t('Email')}</th>
                                                    <th scope="col">{t('Role')}</th>
                                                    <th scope="col">{t('Status')}</th>
                                                    <th scope="col">{t('Devices')}</th>
                                                    <th scope="col">{t('Last Login')}</th>
                                                    <th scope="col">{t('Actions')}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {users.map((user) => (
                                                    <tr key={user.id}>
                                                        <td>
                                                            <div className="d-flex align-items-center">
                                                                <div className="flex-shrink-0">
                                                                    <img src={user.avatar} alt="" className="rounded-circle avatar-xs" />
                                                                </div>
                                                                <div className="flex-grow-1 ms-3">
                                                                    <h6 className="mb-0">{user.name}</h6>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{user.email}</td>
                                                        <td>{getRoleBadge(user.role)}</td>
                                                        <td>{getStatusBadge(user.status)}</td>
                                                        <td>
                                                            <span className="fw-medium">{user.devices}</span>
                                                        </td>
                                                        <td>{user.lastLogin}</td>
                                                        <td>
                                                            <Dropdown>
                                                                <Dropdown.Toggle variant="light" size="sm">
                                                                    <FeatherIcon icon="more-horizontal" className="icon-sm" />
                                                                </Dropdown.Toggle>
                                                                <Dropdown.Menu>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="edit" className="icon-sm me-2" />
                                                                        {t('Edit User')}
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="key" className="icon-sm me-2" />
                                                                        {t('Reset Password')}
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Item>
                                                                        <FeatherIcon icon="shield" className="icon-sm me-2" />
                                                                        {t('Change Role')}
                                                                    </Dropdown.Item>
                                                                    <Dropdown.Divider />
                                                                    <Dropdown.Item className="text-danger">
                                                                        <FeatherIcon icon="trash-2" className="icon-sm me-2" />
                                                                        {t('Delete User')}
                                                                    </Dropdown.Item>
                                                                </Dropdown.Menu>
                                                            </Dropdown>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </Table>
                                    </div>
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>

                    {/* Add User Modal */}
                    <Modal show={showAddModal} onHide={() => setShowAddModal(false)}>
                        <Modal.Header closeButton>
                            <Modal.Title>{t('Add New User')}</Modal.Title>
                        </Modal.Header>
                        <Modal.Body>
                            <Form>
                                <Form.Group className="mb-3">
                                    <Form.Label>{t('Full Name')}</Form.Label>
                                    <Form.Control
                                        type="text"
                                        value={newUser.name}
                                        onChange={(e) => setNewUser({...newUser, name: e.target.value})}
                                    />
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>{t('Email')}</Form.Label>
                                    <Form.Control
                                        type="email"
                                        value={newUser.email}
                                        onChange={(e) => setNewUser({...newUser, email: e.target.value})}
                                    />
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>{t('Role')}</Form.Label>
                                    <Form.Select
                                        value={newUser.role}
                                        onChange={(e) => setNewUser({...newUser, role: e.target.value})}
                                    >
                                        <option value="user">{t('User')}</option>
                                        <option value="admin">{t('Admin')}</option>
                                    </Form.Select>
                                </Form.Group>
                                <Form.Group className="mb-3">
                                    <Form.Label>{t('Status')}</Form.Label>
                                    <Form.Select
                                        value={newUser.status}
                                        onChange={(e) => setNewUser({...newUser, status: e.target.value})}
                                    >
                                        <option value="active">{t('Active')}</option>
                                        <option value="inactive">{t('Inactive')}</option>
                                    </Form.Select>
                                </Form.Group>
                            </Form>
                        </Modal.Body>
                        <Modal.Footer>
                            <Button variant="secondary" onClick={() => setShowAddModal(false)}>
                                {t('Cancel')}
                            </Button>
                            <Button variant="primary" onClick={handleAddUser}>
                                {t('Add User')}
                            </Button>
                        </Modal.Footer>
                    </Modal>
                </Container>
            </div>
        </React.Fragment>
    );
};

UserManagement.layout = (page: any) => <Layout children={page} />;
export default UserManagement;
