import React from 'react';
import { Alert, Card, Col, Row } from 'react-bootstrap';

//Import Icons
import FeatherIcon from "feather-icons-react";

//import images
import illustarator from "../../../../../images/user-illustarator-2.png";
import { Link } from '@inertiajs/react';

const UpgradeAccountNotise = () => {
    return (
        <React.Fragment>
            <Row>
                <Col xs={12}>
                    <Card>
                        <Card.Body className="p-0">
                            <Alert className="alert alert-success border-0 rounded-0 m-0 d-flex align-items-center" role="alert">
                                <FeatherIcon
                                    icon="check-circle"
                                    className="text-success me-2 icon-sm"
                                />
                                <div className="flex-grow-1 text-truncate">
                                    System Status: <b>All Systems Operational</b>
                                </div>
                                <div className="flex-shrink-0">
                                    <Link href="/dashboard" className="text-reset text-decoration-underline"><b>View Details</b></Link>
                                </div>
                            </Alert>

                            <Row className="align-items-end">
                                <Col sm={8}>
                                    <div className="p-3">
                                        <p className="fs-16 lh-base">Power monitoring system is running smoothly with <span className="fw-semibold">12 devices</span> connected and <span className="fw-semibold">8 online</span> <i className="mdi mdi-arrow-right"></i></p>
                                        <div className="mt-3">
                                            <Link href="/dashboard" className="btn btn-primary">View Dashboard</Link>
                                        </div>
                                    </div>
                                </Col>
                                <Col sm={4}>
                                    <div className="px-3">
                                        <img src={illustarator} className="img-fluid" alt="" />
                                    </div>
                                </Col>
                            </Row>
                        </Card.Body>
                    </Card>
                </Col>
            </Row>
        </React.Fragment>
    );
};

export default UpgradeAccountNotise;