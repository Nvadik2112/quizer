import './AnswerItem.css'

// @ts-ignore
const AnswerItem = (props) => {
  const classes =["AnswerItem"];

  if (props.state) {
    classes.push(`AnswerItem--${props.state}`);
  }

  return (
    <li className={classes.join(' ')}
        onClick={()=> props.onAnswerClick(props.answer)}>
      { props.answer }
    </li>
  )
}

export default AnswerItem;