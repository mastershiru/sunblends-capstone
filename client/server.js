const express = require("express");
const cors = require("cors");
const mysql = require("mysql");
const multer = require("multer");
const path = require("path");
const router = express.Router();

const app = express();

app.use(cors({ origin: "http://localhost:3000" }));
app.use(express.json());

// Configure Multer for file uploads
const upload = multer({ dest: "uploads/" });
app.use("/uploads", express.static(path.join(__dirname, "uploads")));

const database = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "sunblends_db",
});

database.connect((err) => {
  if (err) {
    console.error("Database connection failed:", err);
  } else {
    console.log("Connected to database.");
  }
});

//getuser data------------------------------------------------------------------------------------------------------------
app.post("/getUserData", (req, res) => {
  const { email } = req.body;
  const sql =
    // "SELECT Customer_Img, Customer_Name FROM customer WHERE Customer_Email = ?";
    "SELECT Customer_Name, Customer_Email, Customer_Number, Customer_Password, Customer_Img FROM customer WHERE Customer_Email = ?";
  const values = [email];

  database.query(sql, values, (err, data) => {
    if (err) {
      console.error("Error fetching user data:", err);
      return res.status(500).json("Error fetching user data");
    }
    if (data.length === 0) {
      return res.status(404).json("User not found");
    }
    return res.json(data[0]); // Send back the first matching record
  });
});

// Admin login-------------------------------------------------------------------------------------
app.post("/adminLogin", (req, res) => {
  const { email, password } = req.body;

  if (!email || !password) {
    return res.status(400).json("Please provide both admin name and password");
  }

  const sql =
    "SELECT * FROM admin_acc WHERE Admin_Name = ? AND Admin_Password = ?";
  const values = [email, password]; // Assuming 'email' holds the Admin_Name

  database.query(sql, values, (err, data) => {
    if (err) {
      console.error("Error during admin login:", err);
      return res.status(500).json("Server error during admin login");
    }

    if (data.length === 0) {
      return res.status(401).json("Invalid admin credentials");
    }

    // Successfully logged in as admin
    return res.json({
      message: "Admin login successful",
      adminData: {
        Admin_Name: data[0].Admin_Name,
      },
    });
  });
});

//customer login----------------------------------------------------------------------------------------------------
app.post("/login", (req, res) => {
  const sql =
    "SELECT * FROM customer WHERE Customer_Email = ? AND Customer_Password = ?";
  const values = [req.body.email, req.body.password];
  database.query(sql, values, (err, data) => {
    if (err) {
      return res.json("Login failed");
    }
    if (data.length === 0) {
      return res.json("Invalid credentials");
    }
    if (data.length > 0) {
      return res.json("Login Successfully");
    }
    return res.json(data);
  });
});

// Registration route---------------------------------------------------------------------------------------------------------------
app.post("/register", upload.single("image"), (req, res) => {
  const { name, email, phoneNumber, password, confirmPassword } = req.body;
  const imagePath = req.file ? req.file.path : null;

  if (password !== confirmPassword) {
    return res.status(400).json("Passwords do not match");
  }

  const sql =
    "INSERT INTO customer (Customer_Name, Customer_Email, Customer_Number, Customer_Password, Customer_Img) VALUES ( ?,?, ?, ?, ?)";
  const values = [name, email, phoneNumber, password, imagePath];

  database.query(sql, values, (err) => {
    if (err) {
      if (err.code === "ER_DUP_ENTRY") {
        // Handle duplicate email error
        return res
          .status(400)
          .json({ code: "ER_DUP_ENTRY", message: "Email already registered" });
      }
      console.error("Error during registration:", err);
      return res.status(500).json("Error during registration");
    }
    res.json("Registration successful");
  });
});

//google login---------------------------------------------------------------------------------------------------
app.post("/googleLogin", (req, res) => {
  const { Customer_Name, Customer_Email, Customer_Img } = req.body;

  // Check if the user already exists in the database
  const checkSql = "SELECT * FROM customer WHERE Customer_Email = ?";
  const checkValues = [Customer_Email];

  database.query(checkSql, checkValues, (err, data) => {
    if (err) {
      console.error("Error checking user:", err);
      return res.status(500).json("Error checking user");
    }

    if (data.length > 0) {
      // User already exists, update user data if necessary
      return res.json("User already exists, logging in...");
    } else {
      // Insert new user into the database
      const insertSql =
        "INSERT INTO customer (Customer_Name, Customer_Email, Customer_Img) VALUES (?, ?, ?)";
      const insertValues = [Customer_Name, Customer_Email, Customer_Img];

      database.query(insertSql, insertValues, (err) => {
        if (err) {
          console.error("Error inserting user:", err);
          return res.status(500).json("Error inserting user");
        }
        return res.json("User registered successfully");
      });
    }
  });
});

// ///checkout---------------------------------------------------------------------------------------------
app.post("/checkout", (req, res) => {
  const {
    email,
    orderDateTime,
    orderStatus,
    orderType,
    paymentMethod,
    deliveryMethod,
    totalAmount,
    notes,
  } = req.body;

  // Validate required fields
  if (
    !email ||
    !orderDateTime ||
    !paymentMethod ||
    !deliveryMethod ||
    !totalAmount
  ) {
    return res.status(400).json({ message: "Missing required fields." });
  }

  // Fetch Customer_ID from email
  const fetchCustomerIdSql =
    "SELECT Customer_ID FROM customer WHERE Customer_Email = ?";
  database.query(fetchCustomerIdSql, [email], (err, data) => {
    if (err) {
      console.error("Error fetching customer:", err);
      return res.status(500).json({ message: "Error fetching customer." });
    }

    if (data.length === 0) {
      return res.status(404).json({ message: "Customer not found." });
    }

    const Customer_ID = data[0].Customer_ID;

    // Insert the order into the database and retrieve the Order_ID (auto-incremented)
    const insertOrderSql = `
  INSERT INTO online_orders (Customer_ID, Order_DateTime, Order_Status, Order_Type, Payment_Method, Delivery_Method, Total_Payment, Notes)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?)
`;
    const values = [
      Customer_ID,
      orderDateTime,
      orderStatus,
      orderType,
      paymentMethod,
      deliveryMethod,
      totalAmount,
      notes || null, // Insert null if notes is empty or undefined
    ];

    database.query(insertOrderSql, values, (err, result) => {
      if (err) {
        console.error("Error inserting order:", err);
        return res.status(500).json({ message: "Error placing the order." });
      }

      // Get the auto-generated Order_ID (insertId in MySQL)
      const orderID = result.insertId; // For MySQL, result.insertId contains the last inserted ID

      if (!orderID) {
        return res.status(500).json({ message: "Order ID not generated." });
      }

      return res.json({
        message: "Order placed successfully!",
        Order_ID: orderID,
      });
    });
  });
});

// Dine in orders----------------------------------------------------------------------------------------------
app.post("/insertDineinOrder", (req, res) => {
  const {
    customerName,
    orderDateTime,
    orderStatus = "Pending",
    orderType = "Dine-in",
    totalPayment,
    orderItems,
  } = req.body;

  if (
    !customerName ||
    !orderDateTime ||
    !totalPayment ||
    totalPayment <= 0 ||
    !orderItems ||
    orderItems.length === 0
  ) {
    return res.status(400).json({
      message:
        "Invalid input. Ensure all required fields are provided and valid.",
    });
  }

  // Insert the main order
  const orderSql = `
    INSERT INTO walkin_orders (Customer_Name, Order_DateTime, Order_Status, Order_Type, Total_Payment)
    VALUES (?, ?, ?, ?, ?)
  `;
  const orderValues = [
    customerName,
    orderDateTime,
    orderStatus,
    orderType,
    totalPayment,
  ];

  database.query(orderSql, orderValues, (orderErr, orderResult) => {
    if (orderErr) {
      console.error("Error inserting dine-in order:", orderErr);
      return res
        .status(500)
        .json({ message: "Error inserting dine-in order." });
    }

    const orderID = orderResult.insertId; // Get the generated Order_ID

    // Prepare SQL for inserting order items
    const itemsSql = `
      INSERT INTO order_items (Order_ID, Customer_Email, Customer_Name, Customer_Number, Item_Img, Item_Title, Item_Quantity, Item_Price)
      VALUES ?
    `;

    const itemsValues = orderItems.map((item) => [
      orderID,
      null, // Customer_Email (not provided)
      customerName,
      null, // Customer_Number (not provided)
      item.Item_Img,
      item.Item_Title,
      item.Item_Quantity,
      item.Item_Price,
    ]);

    database.query(itemsSql, [itemsValues], (itemsErr) => {
      if (itemsErr) {
        console.error("Error inserting order items:", itemsErr);
        return res
          .status(500)
          .json({ message: "Error inserting order items." });
      }

      res.status(201).json({
        message: "Order and items submitted successfully.",
        orderID,
      });
    });
  });
});

// app.post("/insertDineinOrder", (req, res) => {
//   const { customerName, orderDateTime, orderStatus, orderType, totalPayment } =
//     req.body;

//   if (!customerName) {
//     return res.status(400).json({ message: "Customer name is required." });
//   }

//   const sql = `
//     INSERT INTO walkin_orders (Customer_Name, Order_DateTime, Order_Status, Order_Type, Total_Payment)
//     VALUES (?, ?, ?, ?, ?)
//   `;
//   const values = [
//     customerName,
//     orderDateTime,
//     orderStatus || "Pending",
//     orderType || "Walk-in",
//     totalPayment,
//   ];

//   database.query(sql, values, (err, result) => {
//     if (err) {
//       console.error("Error inserting dine-in order:", err);
//       return res.status(500).json({ message: "Error inserting dine-in order" });
//     }

//     // The insert ID (auto-incremented orderID) will be in result.insertId
//     res.json({
//       message: "Order submitted successfully",
//       orderID: result.insertId, // Return the auto-generated Order_ID
//     });
//   });
// });

//insert order item------------------------------------------------------------------------------------------
app.post("/insertOrderItem", (req, res) => {
  const {
    orderID,
    customerEmail,
    customerName,
    itemImg,
    itemTitle,
    itemQuantity,
    itemPrice,
  } = req.body;

  // Step 1: Fetch the Customer_Number based on the customerEmail
  const sqlFetchCustomerNumber =
    "SELECT Customer_Number FROM customer WHERE Customer_Email = ?";

  database.query(sqlFetchCustomerNumber, [customerEmail], (err, data) => {
    if (err) {
      console.error("Error fetching customer data:", err);
      return res.status(500).json({ message: "Error fetching customer data" });
    }

    if (data.length === 0) {
      return res.status(404).json({ message: "Customer not found" });
    }

    // Step 2: Retrieve the Customer_Number
    const customerNumber = data[0].Customer_Number;

    // Step 3: Proceed with inserting the order item
    const sqlInsertOrderItem = `
      INSERT INTO order_items (Order_ID, Customer_Email, Customer_Name, Customer_Number, Item_Img, Item_Title, Item_Quantity, Item_Price) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    `;
    const values = [
      orderID,
      customerEmail,
      customerName,
      customerNumber, // Use the fetched customer number
      itemImg,
      itemTitle,
      itemQuantity,
      itemPrice,
    ];

    database.query(sqlInsertOrderItem, values, (err, result) => {
      if (err) {
        console.error("Error inserting order item:", err);
        return res.status(500).json({ message: "Error inserting order item" });
      }
      res.status(200).json({ message: "Order item inserted successfully" });
    });
  });
});

//order details-----------------------------------------------------------------------------------------------------------
app.get("/getOrderItems/:orderId", (req, res) => {
  const orderId = req.params.orderId;

  const query = `
    SELECT Item_ID, Order_ID, Customer_Email, Customer_Name,Customer_Number, Item_Img, Item_Title, Item_Quantity, Item_Price
    FROM order_items
    WHERE Order_ID = ?;
  `;

  database.query(query, [orderId], (err, results) => {
    if (err) {
      console.error("Error fetching order items:", err);
      return res.status(500).json({ error: "Error fetching order items" });
    }

    if (results.length === 0) {
      return res.status(404).json({ error: "Order items not found" });
    }

    res.json({
      orderId: orderId,
      items: results,
    });
  });
});

//display  orders--------------------------------------------------------------------------------------------
app.post("/getHistoryOrders", (req, res) => {
  const { email, customerId } = req.body;

  if (email) {
    // First, fetch the Customer_ID based on the email
    const getCustomerIdSql =
      "SELECT Customer_ID FROM customer WHERE Customer_Email = ?";
    database.query(getCustomerIdSql, [email], (err, data) => {
      if (err) {
        console.error("Error fetching customer ID:", err);
        return res.status(500).json("Error fetching customer ID");
      }

      if (data.length === 0) {
        return res.status(404).json("Customer not found");
      }

      const customerId = data[0].Customer_ID;

      // Now, fetch the orders using the Customer_ID
      const getOrdersSql = "SELECT * FROM online_orders WHERE Customer_ID = ?";
      database.query(getOrdersSql, [customerId], (err, orders) => {
        if (err) {
          console.error("Error fetching orders:", err);
          return res.status(500).json("Error fetching orders");
        }

        return res.json(orders); // Return the orders based on Customer_ID
      });
    });
  } else if (customerId) {
    // If customerId is provided directly, fetch orders using it
    const getOrdersSql = "SELECT * FROM online_orders WHERE Customer_ID = ?";
    database.query(getOrdersSql, [customerId], (err, orders) => {
      if (err) {
        console.error("Error fetching orders:", err);
        return res.status(500).json("Error fetching orders");
      }

      return res.json(orders); // Return the orders based on Customer_ID
    });
  } else {
    return res.status(400).json("Email or Customer_ID is required");
  }
});

// Fetch all orders-------------------------------------------------------------------------------------------
app.get("/getAllOrders", (req, res) => {
  const sql =
    "SELECT Order_ID , Order_DateTime, Total_Payment, Order_Status, Order_Type FROM online_orders UNION SELECT Order_ID , Order_DateTime, Total_Payment, Order_Status, Order_Type  FROM walkin_orders ORDER BY Order_ID DESC"; // Order by Order_ID descending
  database.query(sql, (err, data) => {
    if (err) {
      console.error("Error fetching orders:", err);
      return res.status(500).json("Error fetching orders");
    }
    res.json(data);
  });
});

//update orders ------------------------------------------------------------------------------------
app.post("/updateOrderStatus", (req, res) => {
  const { orderId, newStatus } = req.body;

  if (!orderId || !newStatus) {
    return res.status(400).json("Missing orderId or newStatus");
  }

  // Update the 'orders' table
  const sqlOrders =
    "UPDATE online_orders SET Order_Status = ? WHERE Order_ID = ?";
  database.query(sqlOrders, [newStatus, orderId], (err, result) => {
    if (err) {
      console.error("Error updating order status in orders table:", err);
      return res
        .status(500)
        .json("Error updating order status in orders table");
    }

    // Update the 'walkin_orders' table
    const sqlWalkinOrders =
      "UPDATE walkin_orders SET Order_Status = ? WHERE Order_ID = ?";
    database.query(sqlWalkinOrders, [newStatus, orderId], (err, result) => {
      if (err) {
        console.error(
          "Error updating order status in walkin_orders table:",
          err
        );
        return res
          .status(500)
          .json("Error updating order status in walkin_orders table");
      }

      return res.json("Order status updated successfully");
    });
  });
});

//  menu items-------------------------------------------------------------------------------------------------
app.get("/getMenu", (req, res) => {
  const sql = "SELECT * FROM menu"; // Query all dishes from the menu table
  database.query(sql, (err, data) => {
    if (err) {
      console.error("Error fetching menu:", err);
      return res.status(500).json("Error fetching menu");
    }
    res.json(data); // Send the menu data as a response
  });
});

// // //inserting menu items------------------------------------------------------------------------------------------
app.post("/insertMenuItem", upload.single("image"), (req, res) => {
  const {
    dishTitle,
    dishType,
    dishPersons,
    dishPrice,
    dishRating,
    isAvailable, // This will be "1" for Yes, "0" for No, or undefined for NULL
  } = req.body;

  const dishImg = req.file ? req.file.path : null; // Path of uploaded image file

  // Debugging log to verify the values being sent to the database
  console.log("Inserting menu item:", {
    dishTitle,
    dishType,
    dishPersons,
    dishPrice,
    dishRating,
    isAvailable,
    dishImg,
  });

  const sql = `
    INSERT INTO menu (Dish_Img, Dish_Title, Dish_Type, Dish_Persons, Dish_Price, Dish_Rating, isAvailable)
    VALUES (?, ?, ?, ?, ?, ?, ?)
  `;

  // Set isAvailable to NULL if not provided, or convert "1" / "0" to 1 / 0

  const values = [
    dishImg,
    dishTitle,
    dishType,
    dishPersons,
    dishPrice,
    parseFloat(dishRating), // Ensure Dish_Rating is a valid float
    isAvailable, // 1, 0, or NULL depending on availability
  ];

  database.query(sql, values, (err, result) => {
    if (err) {
      console.error("Error inserting menu item:", err);
      return res
        .status(500)
        .json({ message: "Error inserting menu item", error: err });
    }
    res.json({ message: "Menu item inserted successfully!" });
  });
});

//delete item--------------------------------------------------------------------------------------------------
app.delete("/deleteMenuItem/:id", (req, res) => {
  const { id } = req.params;

  const sql = "DELETE FROM menu WHERE Dish_ID = ?";
  database.query(sql, [id], (err) => {
    if (err) {
      console.error("Error deleting menu item:", err);
      return res.status(500).json("Error deleting menu item");
    }
    res.status(200).json("Menu item deleted successfully");
  });
});
//-----------------------------------------------------------------------------------------------------------------
app.listen(8081, () => {
  console.log("Server is running on port 8081");
});
