import './AnswerItem.css'

// @ts-ignore
const AnswerItem = (props) => {
  const classes =["AnswerItem"];

  if (props.status) {
    classes.push(`AnswerItem--${props.status}`);
  }

  return (
    <li className={classes.join(' ')}
        onClick={()=> props.onAnswerClick(props.index)}>
      { props.answer }
    </li>
  )
}

export default AnswerItem;