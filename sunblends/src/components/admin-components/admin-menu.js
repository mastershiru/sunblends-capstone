import React, { useState, useEffect } from "react";
import Insertitem from "../modal-compinents/insert-item";

function Adminmenu() {
  const [menus, setMenus] = useState([]);
  const [isOpenInsertItem, setIsOpenInsertItem] = useState(false);

  // Fetch menu data from the backend when the component mounts
  useEffect(() => {
    fetchMenuData();
  }, []);

  const fetchMenuData = () => {
    fetch("http://localhost:8081/getMenu")
      .then((response) => response.json())
      .then((data) => setMenus(data))
      .catch((error) => console.error("Error fetching menu data:", error));
  };

  // Handle Delete Item
  const handleDeleteItem = (dishId) => {
    if (window.confirm("Are you sure you want to delete this item?")) {
      fetch(`http://localhost:8081/deleteMenuItem/${dishId}`, {
        method: "DELETE",
      })
        .then((response) => {
          if (response.ok) {
            setMenus(menus.filter((menu) => menu.Dish_ID !== dishId));
          } else {
            alert("Failed to delete the item");
          }
        })
        .catch((error) => console.error("Error deleting menu item:", error));
    }
  };

  return (
    <>
      <div className="row" style={{ justifyContent: "space-between" }}>
        <div className="col-auto">
          <header>
            <h2>Menu</h2>
          </header>
        </div>
        <div className="col-auto">
          <button
            className="insert-btn"
            onClick={() => setIsOpenInsertItem(true)}
          >
            Insert Item
          </button>
        </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Dish ID</th>
            <th></th>
            <th>Title</th>
            <th>Type</th>
            <th>Persons</th>
            <th>Price</th>
            <th>Rating</th>
            <th>Available</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {menus.length > 0 ? (
            menus.map((menu) => (
              <tr key={menu.Dish_ID}>
                <td>{menu.Dish_ID}</td>
                <td>
                  <img
                    src={`http://localhost:8081/${menu.Dish_Img}`}
                    alt={menu.Dish_Title}
                    width="50"
                  />
                </td>
                <td>{menu.Dish_Title}</td>
                <td>{menu.Dish_Type}</td>
                <td>{menu.Dish_Persons}</td>
                <td>₱{menu.Dish_Price}</td>
                <td>{menu.Dish_Rating}</td>
                <td style={{ color: menu.isAvailable ? "green" : "red" }}>
                  {menu.isAvailable ? "Yes" : "No"}
                </td>
                <td>
                  <div className="actions">
                    <div className="row" style={{ alignItemsn: "center" }}>
                      <div className="col-auto">
                        <button className="edit-btn">Edit</button>
                      </div>
                      <div
                        className="col-auto"
                        style={{
                          borderLeft: "2px solid #ccc",
                          paddingLeft: "10px",
                        }}
                      >
                        <button
                          className="del-btn"
                          onClick={() => handleDeleteItem(menu.Dish_ID)}
                        >
                          Delete
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            ))
          ) : (
            <tr>
              <td colSpan="9">No menu items available</td>
            </tr>
          )}
        </tbody>
      </table>

      <Insertitem
        isOpenInsertItem={isOpenInsertItem}
        setIsOpenInsertItem={setIsOpenInsertItem}
        fetchMenuData={fetchMenuData}
      />
    </>
  );
}

export default Adminmenu;
