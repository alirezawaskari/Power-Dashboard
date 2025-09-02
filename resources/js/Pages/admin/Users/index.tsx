import React from 'react';
import { Container, Row, Col, Card, CardBody, Button } from 'react-bootstrap';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { Head, Link } from '@inertiajs/react';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';
import FeatherIcon from 'feather-icons-react';

const Users = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Users') + ' | ' + t('Admin Dashboard')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Users')} pageTitle={t('Admin Dashboard')} />
                    <Row>
                        <Col lg={12}>
                            <Link href="/admin/users/management" className="text-decoration-none">
                                <Card className="border-dashed border-info h-100">
                                    <CardBody className="text-center">
                                        <FeatherIcon icon="users" className="icon-lg text-info mb-3" />
                                        <h5>{t('User Management')}</h5>
                                        <p className="text-muted">{t('Manage system users, roles, and permissions')}</p>
                                        <Button variant="outline-info" size="sm">
                                            {t('Manage Users')}
                                        </Button>
                                    </CardBody>
                                </Card>
                            </Link>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

Users.layout = (page: any) => <Layout children={page} />
export default Users;
