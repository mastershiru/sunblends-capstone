// import React, { useState } from "react";
// import axios from "axios";

// const Insertitem = ({
//   isOpenInsertItem,
//   setIsOpenInsertItem,
//   fetchMenuData,
// }) => {
//   const [formData, setFormData] = useState({
//     dishImage: null,
//     dishTitle: "",
//     dishType: "",
//     dishPersons: "",
//     dishPrice: "",
//     dishRating: "",
//     isAvailable: true,
//   });

//   const handleChange = (e) => {
//     const { name, value } = e.target;
//     setFormData({
//       ...formData,
//       [name]: value,
//     });
//   };

//   const handleFileChange = (e) => {
//     setFormData({
//       ...formData,
//       dishImage: e.target.files[0],
//     });
//   };

//   const handleSubmit = async (e) => {
//     e.preventDefault();

//     // form data for submission
//     const formDataToSend = new FormData();
//     formDataToSend.append("image", formData.dishImage);
//     formDataToSend.append("dishTitle", formData.dishTitle);
//     formDataToSend.append("dishType", formData.dishType);
//     formDataToSend.append("dishPersons", formData.dishPersons);
//     formDataToSend.append("dishPrice", formData.dishPrice);
//     formDataToSend.append("dishRating", formData.dishRating);
//     formDataToSend.append("isAvailable", formData.isAvailable);

//     try {
//       // Send data to the backend
//       const response = await axios.post(
//         "http://localhost:8081/insertMenuItem",
//         formDataToSend,
//         {
//           headers: {
//             "Content-Type": "multipart/form-data",
//           },
//         }
//       );

//       alert("Item Inserted", response.data);
//       fetchMenuData();
//     } catch (error) {
//       alert.error("Error inserting item:", error);
//     }
//   };

//   return (
//     <>
//       {isOpenInsertItem && (
//         <div className="center" id="center">
//           <div className="popup">
//             <div className="back-btn" id="back-btn">
//               <button onClick={() => setIsOpenInsertItem(false)}>Back</button>
//             </div>

//             <div className="insert-item-section" id="insert-item-section">
//               <h2>Insert Item</h2>
//               <form onSubmit={handleSubmit}>
//                 <div className="form-element">
//                   <input type="file" onChange={handleFileChange} />
//                 </div>
//                 <div className="form-element">
//                   <label>Dish Title</label>
//                   <input
//                     type="text"
//                     name="dishTitle"
//                     value={formData.dishTitle}
//                     onChange={handleChange}
//                     placeholder="Dish Title"
//                     required
//                   />
//                 </div>
//                 <div className="form-element">
//                   <label>Dish Type</label>
//                   <input
//                     type="text"
//                     name="dishType"
//                     value={formData.dishType}
//                     onChange={handleChange}
//                     placeholder="Dish Type"
//                     required
//                   />
//                 </div>
//                 <div className="form-element">
//                   <label>Dish Persons</label>
//                   <input
//                     type="text"
//                     name="dishPersons"
//                     value={formData.dishPersons}
//                     onChange={handleChange}
//                     placeholder="Dish Persons"
//                     required
//                   />
//                 </div>
//                 <div className="form-element">
//                   <label>Dish Price</label>
//                   <input
//                     type="text"
//                     name="dishPrice"
//                     value={formData.dishPrice}
//                     onChange={handleChange}
//                     placeholder="Dish Price"
//                     required
//                   />
//                 </div>
//                 <div className="form-element">
//                   <label>Dish Rating</label>
//                   <input
//                     type="text"
//                     name="dishRating"
//                     value={formData.dishRating}
//                     onChange={handleChange}
//                     placeholder="Dish Rating"
//                     required
//                   />
//                 </div>
//                 <div className="form-element">
//                   <button type="submit">Insert</button>
//                 </div>
//               </form>
//             </div>
//           </div>
//         </div>
//       )}
//     </>
//   );
// };

// export default Insertitem;

import React, { useState, useRef } from "react";
import axios from "axios";
import { faXmark } from "@fortawesome/free-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";

const Insertitem = ({
  isOpenInsertItem,
  setIsOpenInsertItem,
  fetchMenuData,
}) => {
  // Define initial form state
  const initialFormData = {
    dishImage: null,
    dishTitle: "",
    dishType: "",
    dishPersons: "",
    dishPrice: "",
    dishRating: "",
    isAvailable: "",
  };

  const [formData, setFormData] = useState(initialFormData);
  const fileInputRef = useRef(null); // Ref for file input

  // Handle text input change
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  // Handle file input change
  const handleFileChange = (e) => {
    setFormData({
      ...formData,
      dishImage: e.target.files[0],
    });
  };

  // Handle form submission
  const handleSubmit = async (e) => {
    e.preventDefault();

    // Validate form data
    if (
      !formData.dishTitle ||
      !formData.dishType ||
      !formData.dishPersons ||
      !formData.dishPrice ||
      !formData.dishRating ||
      formData.isAvailable === null
    ) {
      alert("Please fill in all the required fields.");
      return;
    }

    // Prepare form data for submission
    const formDataToSend = new FormData();

    if (formData.dishImage) {
      formDataToSend.append("image", formData.dishImage);
    }
    formDataToSend.append("dishTitle", formData.dishTitle);
    formDataToSend.append("dishType", formData.dishType);
    formDataToSend.append("dishPersons", formData.dishPersons);
    formDataToSend.append("dishPrice", formData.dishPrice);
    formDataToSend.append("dishRating", formData.dishRating);
    formDataToSend.append("isAvailable", formData.isAvailable);

    try {
      const response = await axios.post(
        "http://localhost:8081/insertMenuItem",
        formDataToSend,
        { headers: { "Content-Type": "multipart/form-data" } }
      );
      alert("Item Inserted Successfully!");
      setFormData(initialFormData); // Reset form data
      fetchMenuData(); // Refresh menu data
      if (fileInputRef.current) {
        fileInputRef.current.value = ""; // Reset file input
      }
    } catch (error) {
      console.error("Error submitting form:", error);
      alert(
        `Error inserting item: ${
          error.response?.data?.message || error.message
        }`
      );
    }
  };

  return (
    <>
      {isOpenInsertItem && (
        <div className="center" id="center">
          <div className="popup">
            <div
              className="close-btn"
              id="close-btn"
              onClick={() => setIsOpenInsertItem(false)}
            >
              <FontAwesomeIcon icon={faXmark} />
            </div>

            <div className="insert-item-section" id="insert-item-section">
              <h2>Insert Item</h2>
              <form onSubmit={handleSubmit}>
                <div className="form-element">
                  <label>Dish Title</label>
                  <input
                    type="text"
                    name="dishTitle"
                    value={formData.dishTitle}
                    onChange={handleChange}
                    placeholder="Dish Title"
                    required
                  />
                </div>
                <div className="form-element">
                  <label>Dish Type</label>
                  <input
                    type="text"
                    name="dishType"
                    value={formData.dishType}
                    onChange={handleChange}
                    placeholder="Dish Type"
                    required
                  />
                </div>
                <div className="form-element">
                  <label>Dish Persons</label>
                  <input
                    type="number"
                    name="dishPersons"
                    value={formData.dishPersons}
                    onChange={handleChange}
                    placeholder="Dish Persons"
                    required
                  />
                </div>
                <div className="form-element">
                  <label>Dish Price</label>
                  <input
                    type="number"
                    name="dishPrice"
                    value={formData.dishPrice}
                    onChange={handleChange}
                    placeholder="Dish Price"
                    required
                  />
                </div>
                <div className="form-element">
                  <label>Dish Rating</label>
                  <input
                    type="number"
                    name="dishRating"
                    value={formData.dishRating}
                    onChange={handleChange}
                    placeholder="Dish Rating"
                    required
                  />
                </div>
                <div className="form-element">
                  <label>Available</label>
                  <select
                    name="isAvailable"
                    className="isAvailable"
                    onChange={handleChange}
                  >
                    <option value="null">Select Availability</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>
                <div className="form-element">
                  <input
                    type="file"
                    onChange={handleFileChange}
                    ref={fileInputRef}
                  />
                </div>
                <div className="form-element">
                  <button type="submit">Insert</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default Insertitem;
