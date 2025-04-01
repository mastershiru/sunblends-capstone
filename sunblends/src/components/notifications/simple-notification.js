// import React, { useState, useEffect } from 'react';
// import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
// import { faXmark, faCheck, faInfo, faExclamationCircle } from "@fortawesome/free-solid-svg-icons";

// const SimpleNotification = ({ message, type = 'info', duration = 5000, onClose }) => {
//   const [visible, setVisible] = useState(true);
  
//   useEffect(() => {
//     console.log("SimpleNotification mounted with message:", message);
//     const timer = setTimeout(() => {
//       setVisible(false);
//       if (onClose) {
//         setTimeout(onClose, 300); // Allow fade out animation to complete
//       }
//     }, duration);
    
//     return () => clearTimeout(timer);
//   }, [duration, onClose, message]);
  
//   const getIcon = () => {
//     switch(type) {
//       case 'success': return faCheck;
//       case 'error': return faExclamationCircle;
//       case 'warning': return faExclamationCircle;
//       default: return faInfo;
//     }
//   };
  
//   const backgroundColor = type === 'success' ? '#d1fae5' : 
//                           type === 'error' ? '#fee2e2' :
//                           type === 'warning' ? '#fef3c7' : '#dbeafe';
                          
//   const iconColor = type === 'success' ? '#10b981' :
//                    type === 'error' ? '#ef4444' :
//                    type === 'warning' ? '#f59e0b' : '#3b82f6';
  
//   return (
//     <div style={{
//       display: 'flex',
//       alignItems: 'center',
//       padding: '1rem',
//       borderRadius: '0.5rem',
//       boxShadow: '0 2px 5px rgba(0,0,0,0.1)',
//       backgroundColor: backgroundColor,
//       opacity: visible ? 1 : 0,
//       transition: 'opacity 300ms ease',
//       maxWidth: '320px',
//       margin: '0.5rem 0'
//     }}>
//       <div style={{ marginRight: '0.75rem', color: iconColor }}>
//         <FontAwesomeIcon icon={getIcon()} />
//       </div>
//       <div style={{ flex: 1 }}>
//         <p style={{ margin: 0, fontSize: '0.875rem', fontWeight: 500 }}>{message}</p>
//       </div>
//       <button 
//         onClick={() => {
//           setVisible(false);
//           if (onClose) setTimeout(onClose, 300);
//         }}
//         style={{
//           marginLeft: '0.5rem',
//           background: 'transparent',
//           border: 'none',
//           color: '#9ca3af',
//           cursor: 'pointer'
//         }}
//       >
//         <FontAwesomeIcon icon={faXmark} />
//       </button>
//     </div>
//   );
// };

// export default SimpleNotification;