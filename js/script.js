document.addEventListener("DOMContentLoaded", function() {
  fetch("get_artworks.php")
      .then(response => response.json())
      .then(data => {
          const gallery = document.getElementById("portfolio-gallery");
          data.forEach(artwork => {
              const card = document.createElement("div");
              card.className = "bg-white rounded-lg shadow-md overflow-hidden";
              card.innerHTML = `
                  <img src="${artwork.image_path}" alt="${artwork.title}" class="w-full h-64 object-cover">
                  <div class="p-4">
                      <h4 class="text-xl font-bold text-gray-900">${artwork.title}</h4>
                      <p class="text-gray-600">${artwork.description}</p>
                  </div>
              `;
              gallery.appendChild(card);
          });
      })
      .catch(error => console.error("Error fetching artworks:", error));
});