import React, { useState, useEffect } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronLeft } from "@fortawesome/free-solid-svg-icons";
import "../../assets/css/modal.css";

const EditProfile = ({ isOpenEditProfile, setIsOpenEditProfile, userData }) => {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [profileData, setProfileData] = useState({
    customer_name: "",
    customer_email: "",
    customer_number: "",
    customer_password: "",
    customer_picture: "",
  });

  // Load userData when component mounts or userData changes
  useEffect(() => {
    if (userData) {
      setProfileData(userData);
      setLoading(false);
    }
  }, [userData]);

  return (
    <>
      {isOpenEditProfile && (
        <div className="center" id="center">
          <div className="popup">
            <div className="close-btn" id="close-btn">
              <FontAwesomeIcon
                icon={faChevronLeft}
                onClick={() => setIsOpenEditProfile(false)} // Close the modal
              />
            </div>

            <div className="edit-profile-section" id="edit-profile-section">
              <h2>Profile</h2>

              {/* Show loading spinner or error message while data is being fetched */}
              {loading ? (
                <div>Loading...</div>
              ) : error ? (
                <div>{error}</div>
              ) : (
                <>
                  <div
                    className="img-frame"
                    style={{ borderRadius: "50%", height: "60px" }}
                  >
                    <img
                      src={
                        profileData?.customer_picture
                          ? profileData.customer_picture.startsWith("http")
                            ? profileData.customer_picture
                            : `http://127.0.0.1:8000/storage/${profileData.customer_picture}`
                          : "/default-profile.png"
                      }
                      alt="Profile"
                      className="profile-img"
                      style={{
                        width: "70px",
                        borderRadius: "50%",
                      }}
                    />
                    <hr />
                  </div>
                  <div
                    className="edit-profile-data"
                    style={{ textAlign: "left", marginTop: "30px" }}
                  >
                    <h6>
                      Name:{" "}
                      <span style={{ fontWeight: "normal" }}>
                        {profileData.customer_name}
                      </span>
                    </h6>
                    <h6>
                      Email:{" "}
                      <span style={{ fontWeight: "normal" }}>
                        {profileData.customer_email}
                      </span>
                    </h6>
                    <h6>
                      Phone:{" "}
                      <span style={{ fontWeight: "normal" }}>
                        {profileData.customer_number}
                      </span>
                    </h6>
                    <h6>
                      Password:{" "}
                      <span style={{ fontWeight: "normal" }}>***********</span>
                    </h6>
                  </div>
                </>
              )}

              <div className="form-element">
                <button>Edit</button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
};

export default EditProfile;
