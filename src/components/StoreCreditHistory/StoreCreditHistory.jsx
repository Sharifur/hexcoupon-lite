import React from 'react';
import { MdOutlineSettings } from "react-icons/md";
import StoreCreditTable from './StoreCreditTable'

const StoreCredit = () => {
    return (
        <>
            <div className="hexStoreCredit mt-4">
                <div className="hexStoreCredit__header">
                    <div className="hexStoreCredit__header__flex">
                        <div className="hexStoreCredit__header__left">
                            <h4 className="hexStoreCredit__header__title">Store Credit History</h4>
                        </div>
                        <div className="hexStoreCredit__header__right">
                            <div className="hexStoreCredit__header__right__flex">
                                <div className="hexStoreCredit__header__right__item">
                                    <div className="btn_wrapper">
                                        <a href="javascritp:void(0)" className="cmn_btn btn_bg_1">Grant Credits</a>
                                    </div>
                                </div>
                                <div className="hexStoreCredit__header__right__item">
                                    <div className="hexStoreCredit__header__right__item__icon">
                                        <a href="javascritp:void(0)" className="cmn_btn btn_bg_1"><MdOutlineSettings /></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
                    
                <StoreCreditTable />

                <div className="table__pagination">
                    Pagination
                </div>                          
            </div>
        </>
    );
};

export default StoreCredit;