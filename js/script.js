document.addEventListener(
    "DOMContentLoaded",
    function () {

        console.log(
            "ServiceFinder JavaScript loaded."
        );

        setMinimumDate();

        autoHideAlerts();

    }
);


function toggleProviderFields() {

    const role =
        document.getElementById("role");

    const fields =
        document.getElementById(
            "provider-fields"
        );

    if (!role || !fields) {
        return;
    }

    if (role.value === "provider") {

        fields.style.display = "block";

    } else {

        fields.style.display = "none";

    }
}


function setMinimumDate() {

    const dateInput =
        document.getElementById(
            "booking_date"
        );

    if (!dateInput) {
        return;
    }

    const today =
        new Date()
            .toISOString()
            .split("T")[0];

    dateInput.min = today;
}


function confirmCancel() {

    return confirm(
        "Are you sure you want to cancel this booking?"
    );
}


function autoHideAlerts() {

    const alerts =
        document.querySelectorAll(
            ".alert"
        );

    alerts.forEach(
        function (alert) {

            setTimeout(
                function () {

                    alert.style.opacity =
                        "0";

                    setTimeout(
                        function () {

                            alert.remove();

                        },
                        500
                    );

                },
                4000
            );

        }
    );
}