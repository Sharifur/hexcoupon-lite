import React, { Component } from 'react';
import { MdMoreHoriz, MdModeEdit, MdDeleteOutline, MdHelpOutline, MdKeyboardArrowLeft, MdKeyboardArrowRight } from 'react-icons/md';
import Actions from '../TableData/Actions/Actions';
import Names from '../TableData/Names/Names';
import Amount from '../TableData/Amount/Amount';
import UseLimit from '../TableData/UseLimit/UseLimit';
import Validity from '../TableData/Validity/Validity';
import StatusBtn from '../TableData/StatusBtn/StatusBtn';
import HelpBtnHover from '../TableData/HelpBtnHover/HelpBtnHover';
import Pagination from '../Global/Pagination/Pagination';

const actionLinik = [
    {  LinkIcon: MdModeEdit, LinkName: 'Edit' },
    { LinkIcon: MdDeleteOutline, LinkName: 'Delete' }
]

class DataTable extends Component {

    render() {      
      return (
        <>
            <div className="custom__table bg__white mt-4">
                <table className='w-100'>
                    <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                            />
                        </th>
                        <th>Customer</th>
                        <th>C. Amount</th>
                        <th>Limit of use</th>
                        <th>Valid till</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Mohammad Shahin' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='169 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='169 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='23 Jun 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn' statusName='Refunded as Credit'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Refunded as Credit' />
                                    </StatusBtn>
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Mazharul I Sujon' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='156 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='156 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='$30 Max. / Each Order' /> </td>
                            <td> <Validity validDate='28 Jun 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn active_btn' statusName='Active & Valid'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Active & Valid' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Rupak Chakrabarty' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='142 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='142 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='02 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn expired_btn' statusName='Expired'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Expired' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Istiak Ahmed' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='95 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='95 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Single Order' /> </td>
                            <td> <Validity validDate='01 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn active_btn' statusName='Active & Valid'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Active & Valid' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Ayhsa P. Nitu' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='89 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='89 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='05 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn pending_btn' statusName='Pending'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Pending' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Shahadat H. Polash' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='83 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='83 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='No Limit' /> </td>
                            <td> <Validity validDate='08 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn delete_btn' statusName='Refunded'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Refunded' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Muhammad Zahid' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='81 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='81 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='092 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn active_btn' statusName='Active & Valid'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Active & Valid' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Riyad Hossain' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='200 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='200 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='25% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='12 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn active_btn' statusName='Active & Valid'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Active & Valid' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Kamrul Ibn Zaman' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='74 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='74 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='20% Cre. / Each Order' /> </td>
                            <td> <Validity validDate='20 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn pending_btn' statusName='Pending'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Pending' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                        <tr >
                            <td>
                                <input
                                    type="checkbox"
                                />
                            </td>
                            <td> <Names fullname='Mushfika Al Nahian' /> </td>
                            <td>
                                <div className="status__flex">
                                    <Amount currentAmount='60 Credits'> 
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='60 Credits' />
                                    </Amount>
                                </div>
                            </td>
                            <td> <UseLimit useLimit='No Limit' /> </td>
                            <td> <Validity validDate='25 Jul 2024' /> </td>
                            <td> 
                                <div className="status__flex">
                                    <StatusBtn statusBtnClass='status_btn expired_btn' statusName='Expired'>
                                        <HelpBtnHover statusHelpBtn={<MdHelpOutline />} statusHelpPara='Expired' />
                                    </StatusBtn>                                     
                                </div>
                            </td>
                            <td>
                                <Actions actionIcon={<MdMoreHoriz />} actionLinik={actionLinik} />                                
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div className="pagination">
                    <Pagination paginationPara='SHOWING PAGES 2 OF 20' paginationLeftArrow={<MdKeyboardArrowLeft/> } paginationRightArrow={<MdKeyboardArrowRight />} />
                </div>
            </div>
        </>
      );
    }
}
  

export default DataTable;