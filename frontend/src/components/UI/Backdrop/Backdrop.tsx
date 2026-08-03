import './Backdrop.css';

interface BackdropProps {
  onClick: () => void;
}

const Backdrop = ({ onClick } : BackdropProps) => {
  return (
    <div className='Backdrop' onClick={onClick} />
  );
};


export default Backdrop;