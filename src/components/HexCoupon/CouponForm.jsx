import React from 'react';
import SingleInput from '../Global/FormComponent/SingleInput/SingleInput';
import SingleSelect from '../Global/FormComponent/SingleSelect/SingleSelect';
import Button from '../Global/Button/Button';
import { Link } from 'react-router-dom';
import GrantCoupon from '../GrantCoupon/GrantCoupon';


const CouponForm = () => {

    const selectOptions = [
        { value: '0', label: 'Automated Coupon' },
        { value: '1', label: 'Automated Coupon' },
        { value: '2', label: 'Automated Coupon' },
        { value: '3', label: 'Automated Coupon' },
        { value: '4', label: 'Automated Coupon' },
    ]
    
    const selectOptions2 = [
        { value: '0', label: 'Bulk Coupon' },
        { value: '1', label: 'Bulk Coupon' },
        { value: '2', label: 'Bulk Coupon' },
        { value: '3', label: 'Bulk Coupon' },
        { value: '4', label: 'Bulk Coupon' },
    ]
    const inputSmallText = 'Maximum numbers of coupon will be generated for this automation in its lifetime';

    return (
        <>
            <div className="hexDashboard__card mt-4 radius-10">
                <div className="hex-grid-container column-sm-2">
                    <div className="grid-item">
                        <SingleSelect options={selectOptions} selectLabel='Automation type' />
                    </div>
                    <div className="grid-item">
                        <SingleSelect options={selectOptions2} selectLabel='Coupon type' />
                    </div>
                </div>
                <div className="hex-grid-container mt-4">
                    <div className="grid-item">
                        <SingleInput inputLabel='Automation name' inputType='text' inputClass='form--control radius-5' inputPlaceholder='e.g. automated credit grants' />
                    </div>
                    <div className="grid-item">
                        <SingleInput inputLabel='Max. coupon limit' inputType='number' inputClass='form--control radius-5' inputPlaceholder='100' inputSmall={inputSmallText} />
                    </div>
                </div>
                <div className="btn_wrapper d-flex border_top_1 mt-4 pt-4">
                    <Button ButtonClass='cmn_btn btn_border radius-5' ButtonText='Back'/>

                    <Link to='/grant-coupon' element={<GrantCoupon />} className='cmn_btn btn_bg_1 radius-5'>
                        {ButtonText='Continue'}
                    </Link>
                </div>
            </div>
        </>
    );
};

export default CouponForm;