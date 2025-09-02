import React from 'react';
import { Container, Row, Col, Card, CardBody, Button } from 'react-bootstrap';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { Head, Link } from '@inertiajs/react';
import Layout from "../../../Layouts";
import { useTranslation } from 'react-i18next';
import FeatherIcon from 'feather-icons-react';

const Analytics = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Analytics') + ' | ' + t('Admin Dashboard')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Analytics')} pageTitle={t('Admin Dashboard')} />
                    <Row>
                        <Col lg={6}>
                            <Link href="/admin/analytics/power-consumption" className="text-decoration-none">
                                <Card className="border-dashed border-success h-100">
                                    <CardBody className="text-center">
                                        <FeatherIcon icon="bar-chart-2" className="icon-lg text-success mb-3" />
                                        <h5>{t('Power Consumption Analytics')}</h5>
                                        <p className="text-muted">{t('Analyze power consumption trends and patterns')}</p>
                                        <Button variant="outline-success" size="sm">
                                            {t('View Power Analytics')}
                                        </Button>
                                    </CardBody>
                                </Card>
                            </Link>
                        </Col>
                        <Col lg={6}>
                            <Link href="/admin/analytics/energy-efficiency" className="text-decoration-none">
                                <Card className="border-dashed border-info h-100">
                                    <CardBody className="text-center">
                                        <FeatherIcon icon="trending-up" className="icon-lg text-info mb-3" />
                                        <h5>{t('Energy Efficiency Analytics')}</h5>
                                        <p className="text-muted">{t('Monitor energy efficiency metrics and optimization')}</p>
                                        <Button variant="outline-info" size="sm">
                                            {t('View Efficiency Analytics')}
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

Analytics.layout = (page: any) => <Layout children={page} />
export default Analytics;
