import React from 'react';
import { Container, Row, Col } from 'react-bootstrap';
import BreadCrumb from '../../../Components/Common/BreadCrumb';
import { Head } from '@inertiajs/react';
import Layout from "../../Layouts"/Layout";
import { useTranslation } from 'react-i18next';

const Documents = () => {
    const { t } = useTranslation();
    return (
        <React.Fragment>
            <Head title={t('Documents') + ' | ' + t('Admin Dashboard')} />
            <div className="page-content">
                <Container fluid>
                    <BreadCrumb title={t('Documents')} pageTitle={t('Admin Dashboard')} />
                    <Row>
                        <Col lg={12}>
                            <div className="card">
                                <div className="card-body">
                                    <h4>{t('System Documentation')}</h4>
                                    <p className="text-muted">{t('System documentation interface coming soon...')}</p>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </Container>
            </div>
        </React.Fragment>
    );
};

Documents.layout = (page: any) => <Layout children={page} />
export default Documents;
