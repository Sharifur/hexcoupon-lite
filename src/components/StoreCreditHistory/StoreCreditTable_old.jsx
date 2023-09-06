import React, { Component } from 'react';
import {MdMoreHoriz} from 'react-icons/md';
// import Actions from '../TableData/Actions/Actions';
// import StatusBtn from '../TableData/StatusBtn/StatusBtn';

class DataTable extends Component {    
    constructor(props) {
        super(props);
        this.state = {
          data: [
            { name: 'Muhammad Shahin', amount: '169 Credits', limitation: '20% Cre. / Each Order', validity: '20 Aug 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz,  selected: false },
            { name: 'Mazharul I Sujon', amount: '156 Credits', limitation: '20% Cre. / Each Order', validity: '23 Nov 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Rupak Chakrabarty', amount: '142 Credits', limitation: '20% Cre. / Each Order', validity: '12 Sep 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Istiak Ahmed', amount: '95 Credits', limitation: '20% Cre. / Each Order', validity: '15 Jul 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Ayhsa P. Nitu', amount: '89 Credits', limitation: '20% Cre. / Each Order', validity: '18 Aug 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz,  selected: false },
            { name: 'Shahadat H. Polash', amount: '83 Credits', limitation: 'No Limit', validity: '08 Jul 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Muhammad Zahid', amount: '81 Credits', limitation: '20% Cre. / Each Order', validity: '09 Jul 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Riyad Hossain', amount: '79 Credits', limitation: '20% Cre. / Each Order', validity: '12 Jul 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Kamrul Ibn Zaman', amount: '74 Credits', limitation: '20% Cre. / Each Order', validity: '13 Jul 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
            { name: 'Mushfika Al Nahian', amount: '60 Credits', limitation: 'No Limit', validity: '14 Nov 2024', statusBtn: 'complete', ActionIcon: MdMoreHoriz, selected: false },
          ],
          selectAll: false,
        };
    }
    
    toggleRowSelection = (index) => {
        const { data } = this.state;
        const updatedData = [...data];
        updatedData[index].selected = !updatedData[index].selected;
        this.setState({
          data: updatedData,
          selectAll: updatedData.every(item => item.selected),
        });
    };
    
    toggleSelectAll = () => {
        const { data, selectAll } = this.state;
        const updatedData = data.map(item => ({ ...item, selected: !selectAll }));
        this.setState({
          data: updatedData,
          selectAll: !selectAll,
        });
    };

    render() {
        const { data, selectAll } = this.state;
        
      return (
        <>
            <div className="custom__table bg__white mt-4">
                <table className='w-100'>
                    <thead>
                    <tr>
                        <th>
                            <input
                                type="checkbox"
                                checked={selectAll}
                                onChange={this.toggleSelectAll}
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
                    {data.map((item, index) => (
                    <tr key={item.name}>
                        <td>
                            <input
                                type="checkbox"
                                checked={item.selected}
                                onChange={() => this.toggleRowSelection(index)}
                            />
                        </td>
                        <td>{item.name}</td>
                        <td>{item.amount}</td>
                        <td>{item.limitation}</td>
                        <td>{item.validity}</td>
                        <td>{item.statusBtn}</td>

                        {/* <td> <StatusBtn /> </td> */}
                        <td>
                            {item.ActionIcon}
                        </td>
                    </tr>
                    ))}
                    </tbody>
                </table>
            </div>
        </>
      );
    }
}
  

export default DataTable;