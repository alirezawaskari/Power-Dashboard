import React from 'react';
import { Col, Container, Row, Card, CardBody, Button } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';
import { Link } from '@inertiajs/react';
import FeatherIcon from 'feather-icons-react';

// Import Components
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import RealTimePowerChart from './RealTimePowerChart';
import DeviceStatusGrid from './DeviceStatusGrid';
import AlertSummary from './AlertSummary';
import QuickActions from './QuickActions';
import EnergyEfficiency from './EnergyEfficiency';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';

const UserDashboard = () => {
    const { t } = useTranslation();

    return (
        <React.Fragment>
            <Head title={t('power_dashboard.pages.dashboard.title') + ' | ' + t('Real-Time Monitoring')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('power_dashboard.pages.dashboard.title')} pageTitle={t('Monitoring')} />
                    
                    {/* Quick Access Cards */}
                    <Row className="mb-4">
                        <Col lg={12}>
                            <Card>
                                <CardBody>
                                    <h5 className="card-title mb-3">{t('Quick Access')}</h5>
                                    <Row>
                                        <Col md={3}>
                                            <Link href="/dashboard/support/ticket-chat" className="text-decoration-none">
                                                <Card className="border-dashed border-primary">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="message-circle" className="icon-lg text-primary mb-2" />
                                                        <h6>{t('Support Chat')}</h6>
                                                        <small className="text-muted">{t('Get help with live chat support')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/dashboard/support" className="text-decoration-none">
                                                <Card className="border-dashed border-success">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="help-circle" className="icon-lg text-success mb-2" />
                                                        <h6>{t('Support Tickets')}</h6>
                                                        <small className="text-muted">{t('View and manage support tickets')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/dashboard/devices" className="text-decoration-none">
                                                <Card className="border-dashed border-info">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="monitor" className="icon-lg text-info mb-2" />
                                                        <h6>{t('Devices')}</h6>
                                                        <small className="text-muted">{t('Manage your power monitoring devices')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                        <Col md={3}>
                                            <Link href="/dashboard/alerts" className="text-decoration-none">
                                                <Card className="border-dashed border-warning">
                                                    <CardBody className="text-center">
                                                        <FeatherIcon icon="bell" className="icon-lg text-warning mb-2" />
                                                        <h6>{t('Alerts')}</h6>
                                                        <small className="text-muted">{t('View system alerts and notifications')}</small>
                                                    </CardBody>
                                                </Card>
                                            </Link>
                                        </Col>
                                    </Row>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>
                    
                    {/* Real-time Power Overview */}
                    <Row>
                        <Col xxl={8}>
                            <RealTimePowerChart />
                        </Col>
                        <Col xxl={4}>
                            <AlertSummary />
                        </Col>
                    </Row>

                    {/* Device Status and Quick Actions */}
                    <Row>
                        <Col xxl={8}>
                            <DeviceStatusGrid />
                        </Col>
                        <Col xxl={4}>
                            <QuickActions />
                            <EnergyEfficiency />
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

UserDashboard.layout = (page: any) => <Layout children={page} />
export default UserDashboard;