document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector("input[name='search']");
    if (!searchInput) return; // prevent crash on user pages

    const resultsBox = document.createElement("div");
    resultsBox.style.border = "1px solid #ccc";
    resultsBox.style.background = "#fff";
    resultsBox.style.position = "absolute";
    resultsBox.style.zIndex = "1000";
    resultsBox.style.width = searchInput.offsetWidth + "px";

    searchInput.parentNode.style.position = "relative";
    searchInput.parentNode.appendChild(resultsBox);

    searchInput.addEventListener("keyup", () => {
        const query = searchInput.value.trim();

        if (query.length < 2) {
            resultsBox.innerHTML = "";
            return;
        }

        fetch("/SRMS/public/search_api.php?q=" + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultsBox.innerHTML = "";

                if (!data.results || data.results.length === 0) {
                    resultsBox.innerHTML = "<div style='padding:5px;'>No results</div>";
                    return;
                }

                data.results.forEach(student => {
                    const item = document.createElement("div");
                    item.textContent = student.first_name + " " + student.last_name;
                    item.style.padding = "5px";
                    item.style.cursor = "pointer";

                    item.addEventListener("click", () => {
                        searchInput.value = student.first_name + " " + student.last_name;
                        resultsBox.innerHTML = "";
                    });

                    resultsBox.appendChild(item);
                });
            })
            .catch(err => {
                console.error("AJAX error:", err);
            });
    });
});