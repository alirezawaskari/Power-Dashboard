import React from 'react';
import { Card, Col, Row } from 'react-bootstrap';
import { useTranslation } from 'react-i18next';

//import Images
import illustrator from "@assets/images/illustrator-1.png";
import { Link } from '@inertiajs/react';

const TopReferrals = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Col xl={4} md={6}>
                <Card className="card-height-100">
                    <Card.Header className="align-items-center d-flex">
                        <h4 className="card-title mb-0 flex-grow-1">{t('Power Consumption by Location')}</h4>
                        <div className="flex-shrink-0">
                            <button type="button" className="btn btn-soft-primary btn-sm material-shadow-none">
                                {t('Export Report')}
                            </button>
                        </div>
                    </Card.Header>

                    <Card.Body>

                        <Row className="align-items-center">
                            <Col xs={6}>
                                <h6 className="text-muted text-uppercase fw-semibold text-truncate fs-12 mb-3">{t('Total Power Consumption')}</h6>
                                <h4 className="fs- mb-0">2.45 kW</h4>
                                <p className="mb-0 mt-2 text-muted"><span className="badge bg-success-subtle text-success mb-0">
                                    <i className="ri-arrow-up-line align-middle"></i> 12.5 %
                                </span> {t('vs. previous hour')}</p>
                            </Col>
                            <Col xs={6}>
                                <div className="text-center">
                                    <img src={illustrator} className="img-fluid" alt="" />
                                </div>
                            </Col>
                        </Row>
                        <div className="mt-3 pt-2">
                            <div className="progress progress-lg rounded-pill">
                                <div className="progress-bar bg-primary" role="progressbar" style={{ width: "35%" }} ></div>
                                <div className="progress-bar bg-info" role="progressbar" style={{ width: "25%" }} ></div>
                                <div className="progress-bar bg-success" role="progressbar" style={{ width: "20%" }} ></div>
                                <div className="progress-bar bg-warning" role="progressbar" style={{ width: "15%" }} ></div>
                                <div className="progress-bar bg-danger" role="progressbar" style={{ width: "5%" }} ></div>
                            </div>
                        </div>

                        <div className="mt-3 pt-2">
                            <div className="d-flex mb-2">
                                <div className="flex-grow-1">
                                    <p className="text-truncate text-muted fs-14 mb-0"><i className="mdi mdi-circle align-middle text-primary me-2"></i>{t('Main Building')}</p>
                                </div>
                                <div className="flex-shrink-0">
                                    <p className="mb-0">35.2%</p>
                                </div>
                            </div>
                            <div className="d-flex mb-2">
                                <div className="flex-grow-1">
                                    <p className="text-truncate text-muted fs-14 mb-0"><i className="mdi mdi-circle align-middle text-info me-2"></i>{t('Production Floor')}</p>
                                </div>
                                <div className="flex-shrink-0">
                                    <p className="mb-0">25.8%</p>
                                </div>
                            </div>
                            <div className="d-flex mb-2">
                                <div className="flex-grow-1">
                                    <p className="text-truncate text-muted fs-14 mb-0"><i className="mdi mdi-circle align-middle text-success me-2"></i>{t('Office Area')}</p>
                                </div>
                                <div className="flex-shrink-0">
                                    <p className="mb-0">20.1%</p>
                                </div>
                            </div>
                            <div className="d-flex mb-2">
                                <div className="flex-grow-1">
                                    <p className="text-truncate text-muted fs-14 mb-0"><i className="mdi mdi-circle align-middle text-warning me-2"></i>{t('Server Room')}</p>
                                </div>
                                <div className="flex-shrink-0">
                                    <p className="mb-0">15.3%</p>
                                </div>
                            </div>
                            <div className="d-flex">
                                <div className="flex-grow-1">
                                    <p className="text-truncate text-muted fs-14 mb-0"><i className="mdi mdi-circle align-middle text-danger me-2"></i>{t('Other Areas')}</p>
                                </div>
                                <div className="flex-shrink-0">
                                    <p className="mb-0">3.6%</p>
                                </div>
                            </div>
                        </div>

                        <div className="mt-2 text-center">
                            <Link href="#" className="text-muted text-decoration-underline">{t('Show All Locations')}</Link>
                        </div>

                    </Card.Body>
                </Card>
            </Col>
        </React.Fragment>
    );
};

export default TopReferrals;