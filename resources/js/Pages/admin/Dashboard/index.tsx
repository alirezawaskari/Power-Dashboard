import React from 'react';
import { Col, Container, Row, Card, CardBody, Button } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';
import { Link } from '@inertiajs/react';
import FeatherIcon from 'feather-icons-react';

// Import Components
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import SystemHealthOverview from './SystemHealthOverview';
import UserManagement from './UserManagement';
import PerformanceMetrics from './PerformanceMetrics';
import AlertManagement from './AlertManagement';
import SystemAnalytics from './SystemAnalytics';
import { Head } from '@inertiajs/react';
import Layout from "../../../Layouts";

const AdminDashboard = () => {
    const { t } = useTranslation();

    return (
        <React.Fragment>
            <Head title={t('Admin Dashboard') + ' | ' + t('System Management')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Admin Dashboard')} pageTitle={t('System Management')} />
                    
                    {/* Quick Access Cards */}
                    <Row>
                        <Col lg={12}>
                            <Card>
                                <CardBody>
                                    <h5 className="card-title mb-3">{t('Quick Access')}</h5>
                                    <Row>
                                        <Col md={3}>
                                            <Link href="/admin/monitoring/device-status" className="text-decoration-none">
                                                <Card className="border-dashed border-primary">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="eye" className="icon-lg text-primary mb-2" />
                                                        <h6>{t('Device Status')}</h6>
                                                        <small className="text-muted">{t('Monitor real-time device status')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/admin/analytics/power-consumption" className="text-decoration-none">
                                                <Card className="border-dashed border-success">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="bar-chart-2" className="icon-lg text-success mb-2" />
                                                        <h6>{t('Power Analytics')}</h6>
                                                        <small className="text-muted">{t('View power consumption data')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/admin/monitoring/alert-logs" className="text-decoration-none">
                                                <Card className="border-dashed border-warning">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="alert-triangle" className="icon-lg text-warning mb-2" />
                                                        <h6>{t('Alert Logs')}</h6>
                                                        <small className="text-muted">{t('Review system alerts')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/admin/users/management" className="text-decoration-none">
                                                <Card className="border-dashed border-info">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="users" className="icon-lg text-info mb-2" />
                                                        <h6>{t('User Management')}</h6>
                                                        <small className="text-muted">{t('Manage system users')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                    </Row>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>
                    
                    {/* System Health and User Management */}
                    <Row>
                        <Col lg={6}>
                            <SystemHealthOverview />
                        </Col>
                        <Col lg={6}>
                            <UserManagement />
                        </Col>
                    </Row>

                    {/* Performance and Alert Management */}
                    <Row>
                        <Col lg={6}>
                            <PerformanceMetrics />
                        </Col>
                        <Col lg={6}>
                            <AlertManagement />
                        </Col>
                    </Row>

                    {/* System Analytics */}
                    <Row>
                        <Col lg={12}>
                            <SystemAnalytics />
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

AdminDashboard.layout = (page: any) => <Layout children={page} />
export default AdminDashboard;
