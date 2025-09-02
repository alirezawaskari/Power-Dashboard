import React from 'react';
import { Card, Col, Container, Row } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { BasicLineCharts, ZoomableTimeseries, LinewithDataLabels } from '../../templates/Charts/ApexCharts/LineCharts/LineCharts';
import { BasicAreaCharts } from '../../templates/Charts/ApexCharts/AreaCharts/AreaCharts';
import { useTranslation } from 'react-i18next';

const PowerConsumption = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Power Consumption Analytics') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Power Consumption Analytics')} pageTitle={t('Analytics')} />
                    
                    <Row>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Daily Power Consumption')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <BasicLineCharts dataColors='["--vz-primary"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Weekly Consumption Trend')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <ZoomableTimeseries dataColors='["--vz-success"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Monthly Comparison')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <LinewithDataLabels dataColors='["--vz-primary", "--vz-success"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Peak Usage Analysis')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <BasicAreaCharts dataColors='["--vz-warning"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

PowerConsumption.layout = (page: any) => <Layout children={page} />;
export default PowerConsumption;
