import React from 'react';


const Pagination = ({paginationPara, paginationLeftArrow, paginationRightArrow}) => { 
  return (
    <>
      <div className="pagination__flex">
          <div className="pagination__left">
            <p className="pagination__para">{paginationPara}</p>
          </div>
          <div className="pagination__right">
            <ul className='pagination_list'>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__button'>{paginationLeftArrow}</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__link'>1</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__link'>2</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__link'>3</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__link'>4</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__link'>5</a></li>
              <li className='pagination_list__item'><a href="#0" className='pagination_list__item__button'>{paginationRightArrow}</a></li>
            </ul>
          </div>        
      </div>
    </>
  );
};

export default Pagination;
