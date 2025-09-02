import React from 'react';
import { Container, Row, Col, Card, CardBody } from 'react-bootstrap';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { Head, Link } from '@inertiajs/react';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';
import FeatherIcon from 'feather-icons-react';

const Monitoring = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Monitoring') + ' | ' + t('Admin Dashboard')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Monitoring')} pageTitle={t('Admin Dashboard')} />
                    <Row>
                        <Col lg={6}>
                            <Link href="/admin/monitoring/device-status" className="text-decoration-none">
                                <Card className="border-dashed border-primary h-100">
                                    <CardBody className="text-center">
                                        <FeatherIcon icon="eye" className="icon-lg text-primary mb-3" />
                                        <h5>{t('Device Status')}</h5>
                                        <p className="text-muted">{t('Monitor real-time device status and power consumption')}</p>
                                        <Button variant="outline-primary" size="sm">
                                            {t('View Device Status')}
                                        </Button>
                                    </CardBody>
                                </Card>
                            </Link>
                        </Col>
                        <Col lg={6}>
                            <Link href="/admin/monitoring/alert-logs" className="text-decoration-none">
                                <Card className="border-dashed border-warning h-100">
                                    <CardBody className="text-center">
                                        <FeatherIcon icon="alert-triangle" className="icon-lg text-warning mb-3" />
                                        <h5>{t('Alert Logs')}</h5>
                                        <p className="text-muted">{t('Review and manage system alerts and notifications')}</p>
                                        <Button variant="outline-warning" size="sm">
                                            {t('View Alert Logs')}
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

Monitoring.layout = (page: any) => <Layout children={page} />
export default Monitoring;
