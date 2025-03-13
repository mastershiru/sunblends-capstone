import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import {
  faXmark,
  faCheck,
  faSpinner,
  faBan,
  faCircleInfo,
  faCircleCheck,
} from "@fortawesome/free-solid-svg-icons";

const NotificationsCenter = ({
  isOpen,
  onClose,
  notifications,
  onMarkAllAsRead,
  onViewDetails,
}) => {
  if (!isOpen) return null;

  // Helper to get appropriate icon for status
  const getStatusIcon = (status) => {
    switch (status) {
      case "completed":
        return faCircleCheck;
      case "ready":
        return faCheck;
      case "processing":
        return faSpinner;
      case "cancelled":
        return faBan;
      default:
        return faCircleInfo;
    }
  };

  // Helper to get appropriate color for status
  const getStatusColor = (status) => {
    switch (status) {
      case "completed":
      case "ready":
        return "#10b981"; // green
      case "processing":
        return "#3b82f6"; // blue
      case "cancelled":
        return "#ef4444"; // red
      default:
        return "#6b7280"; // gray
    }
  };

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      style={{
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        bottom: 0,
        backgroundColor: "rgba(0, 0, 0, 0.5)",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
        zIndex: 9999,
      }}
    >
      <div
        style={{
          backgroundColor: "white",
          borderRadius: "0.5rem",
          maxWidth: "600px",
          width: "90%",
          maxHeight: "80vh",
          boxShadow: "0 10px 25px rgba(0, 0, 0, 0.2)",
          display: "flex",
          flexDirection: "column",
          overflow: "hidden",
        }}
      >
        {/* Header */}
        <div
          style={{
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            borderBottom: "1px solid #e5e7eb",
            padding: "1rem",
          }}
        >
          <h2 style={{ margin: 0, fontSize: "1.25rem", fontWeight: "bold" }}>
            Notifications
          </h2>
          <div style={{ display: "flex", alignItems: "center", gap: "10px" }}>
            <button
              onClick={onMarkAllAsRead}
              style={{
                fontSize: "0.875rem",
                color: "#ff8243",
                border: "none",
                background: "none",
                cursor: "pointer",
                display: "flex",
                alignItems: "center",
                gap: "0.25rem",
                textAlign: "right",
                padding: "0",
                margin: "0",
              }}
            >
              <FontAwesomeIcon icon={faCheck} size="sm" />
              Mark all as read
            </button>
            <button
              onClick={onClose}
              style={{
                background: "none",
                border: "none",
                cursor: "pointer",
                fontSize: "1.25rem",
                color: "#6b7280",
                textAlign: "right",
              }}
            >
              <FontAwesomeIcon icon={faXmark} />
            </button>
          </div>
        </div>

        {/* Notification List */}
        <div
          style={{
            padding: "0.5rem",
            overflowY: "auto",
            maxHeight: "calc(80vh - 60px)", // Subtract header height
            flexGrow: 1,
          }}
        >
          {notifications.length === 0 ? (
            <div
              style={{
                display: "flex",
                flexDirection: "column",
                alignItems: "center",
                justifyContent: "center",
                padding: "2rem",
                color: "#9ca3af",
              }}
            >
              <FontAwesomeIcon
                icon={faCircleInfo}
                style={{ fontSize: "2rem", marginBottom: "1rem" }}
              />
              <p>No notifications yet</p>
            </div>
          ) : (
            notifications.map((notification) => (
              <div
                key={notification.id}
                onClick={() => onViewDetails(notification.order_id)}
                style={{
                  padding: "1rem",
                  borderBottom: "1px solid #f3f4f6",
                  cursor: "pointer",
                  backgroundColor: notification.read
                    ? "transparent"
                    : "rgba(255, 130, 67, 0.05)",
                  display: "flex",
                  gap: "1rem",
                  transition: "background-color 0.2s",
                  borderRadius: "0.25rem",
                  margin: "0.25rem 0",
                }}
                onMouseOver={(e) =>
                  (e.currentTarget.style.backgroundColor = notification.read
                    ? "#f9fafb"
                    : "rgba(255, 130, 67, 0.1)")
                }
                onMouseOut={(e) =>
                  (e.currentTarget.style.backgroundColor = notification.read
                    ? "transparent"
                    : "rgba(255, 130, 67, 0.05)")
                }
              >
                <div
                  style={{
                    flexShrink: 0,
                    width: "40px",
                    height: "40px",
                    borderRadius: "50%",
                    backgroundColor: getStatusColor(notification.status),
                    display: "flex",
                    alignItems: "center",
                    justifyContent: "center",
                    color: "white",
                  }}
                >
                  <FontAwesomeIcon icon={getStatusIcon(notification.status)} />
                </div>
                <div style={{ flexGrow: 1 }}>
                  <div
                    style={{ fontWeight: notification.read ? "400" : "600" }}
                  >
                    {notification.message}
                  </div>
                  <div
                    style={{
                      display: "flex",
                      justifyContent: "space-between",
                      fontSize: "0.75rem",
                      color: "#6b7280",
                      marginTop: "0.25rem",
                    }}
                  >
                    <span>
                      {new Date(notification.timestamp).toLocaleString([], {
                        month: "short",
                        day: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                      })}
                    </span>
                    <span
                      style={{
                        padding: "0.1rem 0.5rem",
                        borderRadius: "9999px",
                        backgroundColor:
                          getStatusColor(notification.status) + "20", // Add 20% opacity
                        color: getStatusColor(notification.status),
                        fontWeight: "500",
                        textTransform: "capitalize",
                      }}
                    >
                      {notification.status}
                    </span>
                  </div>
                </div>
              </div>
            ))
          )}
        </div>

        {/* Footer */}
        <div
          style={{
            borderTop: "1px solid #e5e7eb",
            padding: "1rem",
            textAlign: "center",
          }}
        >
          <button
            onClick={onClose}
            style={{
              padding: "0.5rem 2rem",
              backgroundColor: "#ff8243",
              color: "white",
              border: "none",
              borderRadius: "0.25rem",
              cursor: "pointer",
              fontWeight: "500",
              transition: "background-color 0.2s",
            }}
            onMouseOver={(e) =>
              (e.currentTarget.style.backgroundColor = "#f97316")
            }
            onMouseOut={(e) =>
              (e.currentTarget.style.backgroundColor = "#ff8243")
            }
          >
            Close
          </button>
        </div>
      </div>
    </div>
  );
};

export default NotificationsCenter;
