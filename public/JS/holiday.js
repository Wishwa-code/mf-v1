// public/JS/holiday.js

let dueSkipProgressInterval = null;

$(document).ready(function () {

    // DataTables for holiday list
    $('#holiday_table').DataTable();

    // Select2 for dropdowns
    $('.select2-single').select2({
        width: '100%',
        placeholder: 'Select an option',
        allowClear: true
    });

    // Select all holidays checkbox
    $('#selectAllHolidays').on('change', function () {
        const checked = $(this).is(':checked');
        $('.holiday-check').prop('checked', checked).trigger('change');
    });

// Update selected count + handle indeterminate state
    $(document).on('change', '.holiday-check', function () {
        const total = $('.holiday-check').length;
        const selected = $('.holiday-check:checked').length;

        $('#selectedHolidayCount').text(selected + ' selected');

        const all = (selected === total);
        const none = (selected === 0);

        $('#selectAllHolidays')
            .prop('checked', all)
            .prop('indeterminate', !all && !none);
    });


    // Focus on reason input initially
    $("#account_name").focus().select();

    // Enter key press to submit holiday
    $(document).keypress(function (e) {
        if (e.which === 13) {
            e.preventDefault();

            if (e.target.id === "account_name") {
                if (validateRequired("account_name")) {
                    validateSubmitBank(e);
                }
            }
        }
    });

    // SkipFor dropdown: show/hide targets
    $('#skipFor').on('change', function () {
        const value = $(this).val();
        $('.target-dropdown').addClass('d-none');

        switch (value) {
            case 'loan':
                $('#loanSelectWrapper').removeClass('d-none');
                break;
            case 'branch':
                $('#branchSelectWrapper').removeClass('d-none');
                break;
            case 'center':
                $('#centerSelectWrapper').removeClass('d-none');
                break;
            case 'product':
                $('#productSelectWrapper').removeClass('d-none');
                break;
        }
    });

    // Poya days + weekend buttons
    const year = new Date().getFullYear();

    $('#addPoyaDays').click(function () {
        fetch('/poya-days')
            .then(response => response.json())
            .then(data => {
                data.forEach(day => {
                    const year = day[0];
                    const month = String(day[1]).padStart(2, '0');
                    const date = String(day[2]).padStart(2, '0');
                    const fullDate = `${year}-${month}-${date}`;

                    if (!isDateInTable(fullDate)) {
                        $('#poya-days').append(
                            `<tr><td>${fullDate}</td><td>Full Moon</td></tr>`
                        );
                    }
                });

                Swal.fire({
                    icon: "success",
                    title: "Poya Days added!",
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(error => {
                console.error('Error fetching Poya days:', error);
                Swal.fire({
                    icon: "error",
                    title: "Failed to load Poya Days",
                    text: "Please try again later."
                });
            });
    });

    $('#addWeekendDays').click(function () {
        const weekendDays = calculateWeekendDays(year);
        weekendDays.forEach(day => {
            $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
        });
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Weekend days added!",
            showConfirmButton: false,
            timer: 1500
        });
    });

    $('#addOnlySaturdays').click(function () {
        const saturdays = calculateWeekendDays(year).filter(d => d.description === 'Saturday');
        saturdays.forEach(day => {
            $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
        });
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Saturdays added!',
            showConfirmButton: false,
            timer: 1500
        });
    });

    $('#addOnlySundays').click(function () {
        const sundays = calculateWeekendDays(year).filter(d => d.description === 'Sunday');
        sundays.forEach(day => {
            $('#poya-days').append(`<tr><td>${day.date}</td><td>${day.description}</td></tr>`);
        });
        Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Sundays added!',
            showConfirmButton: false,
            timer: 1500
        });
    });

    // Save Poya Days to DB
    $('#savePoyaDays').click(function () {
        const poyaDays = [];
        $("#poya-days tr").each(function () {
            const date = $(this).find("td:first").text();
            const description = $(this).find("td:last").text();
            if (date) {
                poyaDays.push({ date, description });
            }
        });

        if (poyaDays.length === 0) {
            Swal.fire("Info", "No Poya / weekend days to save.", "info");
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: "Do you want to save all Poya Days?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Save them!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "/poya-days/save",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: { poyaDays },
                    success: function (response) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: response.message || "Saved!",
                            showConfirmButton: false,
                            timer: 1500,
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function () {
                        Swal.fire("Error!", "Failed to save Poya Days!", "error");
                    },
                });
            }
        });
    });

    // Generate Due Skip button
    $('#btnGenerateDueSkip').on('click', function () {
        generateDueSkip();
    });
});

/**
 * Check if date already exists in Poya table
 */
function isDateInTable(date) {
    let exists = false;
    $('#poya-days tr').each(function () {
        const existingDate = $(this).find('td:first').text();
        if (existingDate === date) {
            exists = true;
            return false;
        }
    });
    return exists;
}

/**
 * Calculate all weekend days for a given year
 */
function calculateWeekendDays(year) {
    const weekendDays = [];
    const startDate = new Date(`${year}-01-01`);
    const endDate = new Date(`${year}-12-31`);
    let currentDate = startDate;

    while (currentDate <= endDate) {
        const dayOfWeek = currentDate.getDay();
        if (dayOfWeek === 0 || dayOfWeek === 6) {
            const formattedDate = currentDate.toISOString().split('T')[0];
            if (!isDateInTable(formattedDate)) {
                weekendDays.push({
                    date: formattedDate,
                    description: dayOfWeek === 0 ? "Sunday" : "Saturday"
                });
            }
        }
        currentDate.setDate(currentDate.getDate() + 1);
    }

    return weekendDays;
}

/**
 * Validate and submit holiday
 */
const validateSubmitHoliday = (event) => {
    event.preventDefault();

    let err = 0;
    let arr = ["holiday_date", "account_name"];
    err = check_validate(arr, err);

    if (err === 0) {
        saveHoliday(event);
    } else {
        Swal.fire("Error!", "Please fill the required fields !", "error");
        return false;
    }
};

const saveHoliday = (e) => {
    e.preventDefault();

    const date = $("#holiday_date").val();
    const reason = $("#account_name").val();

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to save this Holiday ?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Save it!",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: "/holidays",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: { date, reason },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        if (data.id === "1") {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire("Error!", "This Date already exists!", "error");
                        }
                    } else {
                        Swal.fire("Error!", "Failed to save data!", "error");
                    }
                },
            });
        }
    });
};

/**
 * Delete holiday
 */
function deleteHolidays(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/holidays/delete/${id}`,
                type: 'DELETE',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire(
                        'Error!',
                        'Something went wrong. Please try again later.',
                        'error'
                    );
                }
            });
        }
    });
}

/**
 * Progress bar helpers
 */
function startDueSkipProgress() {
    const $modal = $('#dueSkipProgressModal');
    const $bar   = $('#dueSkipProgressBar');
    const $text  = $('#dueSkipStatusText');

    $bar.css('width', '0%').text('0%');
    $text.text('Initializing due skip process...');

    $modal.modal('show');

    let progress = 0;
    dueSkipProgressInterval = setInterval(() => {
        if (progress < 90) {
            progress += 5;
            $bar.css('width', progress + '%').text(progress + '%');
        }
    }, 300);
}

function finishDueSkipProgress(message) {
    clearInterval(dueSkipProgressInterval);
    const $bar  = $('#dueSkipProgressBar');
    const $text = $('#dueSkipStatusText');

    $bar.css('width', '100%').text('100%');
    $text.text(message || 'Completed.');
}

/**
 * Generate Due Skip
 */
function generateDueSkip() {
    const skipFor   = $('#skipFor').val();
    const skipType  = $('#skipType').val();

    // ✅ Selected holiday dates
    const selectedDates = $('.holiday-check:checked').map(function () {
        return $(this).val();
    }).get();

    if (selectedDates.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Select holiday dates',
            text: 'Please select at least one holiday date to process.'
        });
        return;
    }

    let targetValue = null;
    let targetText  = null;

    if (skipFor === 'loan') {
        targetValue = $('#loanSelect').val();
        targetText  = $('#loanSelect option:selected').text();
    } else if (skipFor === 'branch') {
        targetValue = $('#branchSelect').val();
        targetText  = $('#branchSelect option:selected').text();
    } else if (skipFor === 'center') {
        targetValue = $('#centerSelect').val();
        targetText  = $('#centerSelect option:selected').text();
    } else if (skipFor === 'product') {
        targetValue = $('#productSelect').val();
        targetText  = $('#productSelect option:selected').text();
    }

    if (skipFor !== 'all' && (!targetValue || targetValue === '')) {
        Swal.fire("Validation", "Please select a target for the selected mode.", "warning");
        return;
    }

    // ✅ Simple confirm text (no heavy HTML)
    Swal.fire({
        title: 'Generate Due Skip?',
        text: `Selected dates: ${selectedDates.length} | Mode: ${skipFor} | Type: ${skipType}`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Proceed',
    }).then((result) => {
        if (result.isConfirmed) {
            startDueSkipProgress();

            $.ajax({
                url: '/generate-due-skip',
                type: 'POST',
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    skip_for: skipFor,
                    target_id: targetValue,
                    skip_type: skipType,
                    selected_dates: selectedDates, // ✅ send selected holidays
                },
                success: function (response) {
                    finishDueSkipProgress('Due skip processed successfully.');

                    setTimeout(() => {
                        $('#dueSkipProgressModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Done!',
                            text: 'Due skip completed successfully.',
                            showConfirmButton: true,
                        }).then(() => window.location.reload());
                    }, 800);
                },
                error: function (xhr) {
                    finishDueSkipProgress('Failed to process due skip.');
                    setTimeout(() => {
                        $('#dueSkipProgressModal').modal('hide');
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: 'Something went wrong. Please try again.',
                        });
                        console.error(xhr.responseText);
                    }, 600);
                }
            });
        }
    });
}
