/* eslint-disable react/prop-types */
// import '../../../assets/sass/component/switch.scss'


const Switch = ({ className, isSwitchText, switchText, switchPosition = "left", isChecked,onSwitchChange,...restProps }) => {


	const handleSwitchChange = () => {
		if (onSwitchChange) {
			onSwitchChange(!isChecked);
		}
	};

	return (
        <div className={`switchWrap ${className ?? ''}`}>
            <label className={`switchWrap__label ${switchPosition ?? ''}`}>
                <div className="switchWrap__main">
                    <input type="checkbox" checked={isChecked} onChange={handleSwitchChange} {...restProps} />
                    <div className="slideSwitch rounded"></div>
                </div>
                {isSwitchText && <span className="text-sm font-medium text-gray-900 dark:text-gray-300">{switchText}</span>}
            </label>

        </div>
    );
};

export default Switch;

