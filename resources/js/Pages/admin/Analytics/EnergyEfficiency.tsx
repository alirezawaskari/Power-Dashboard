import React from 'react';
import { Card, Col, Container, Row } from 'react-bootstrap';
import { Head } from '@inertiajs/react';
import Layout from '../../../Layouts';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { BasicPieCharts } from '../../templates/Charts/ApexCharts/PieCharts/PieCharts';
import { BasicRadarCharts } from '../../templates/Charts/ApexCharts/RadarCharts/RadarCharts';
import { BasicBarCharts } from '../../templates/Charts/ApexCharts/BarCharts/BarCharts';
import { useTranslation } from 'react-i18next';

const EnergyEfficiency = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Energy Efficiency Analytics') + ' | Power Dashboard'} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Energy Efficiency Analytics')} pageTitle={t('Analytics')} />
                    
                    <Row>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Energy Distribution')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <BasicPieCharts dataColors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                        <Col lg={6}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Efficiency Metrics')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <BasicRadarCharts dataColors='["--vz-primary", "--vz-success"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <Col lg={12}>
                            <Card>
                                <Card.Header>
                                    <h4 className="card-title mb-0">{t('Monthly Efficiency Comparison')}</h4>
                                </Card.Header>
                                <Card.Body>
                                    <BasicBarCharts dataColors='["--vz-primary", "--vz-success"]' />
                                </Card.Body>
                            </Card>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

EnergyEfficiency.layout = (page: any) => <Layout children={page} />;
export default EnergyEfficiency;
