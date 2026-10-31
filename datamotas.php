
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motocross Shop | Catalog</title>

   <link rel="stylesheet" href="style.css">
      
</head>

<body>

    <!-- HEADER -->

    <header>
        <div class="logo">
            MOTO<span>CROSS</span> SHOP
        </div>

        <nav>
            <a href="welcome.php" class="backbutton">← BACK</a>
        </nav>
    </header>

    <!-- HERO -->

    <section class="hero">
        <h1>
            FIND YOUR NEXT <span>RIDE.</span>
        </h1>

        <p>
            Explore our motorcycle catalog.
            Discover different brands and models
            and find the perfect bike for your next adventure.
        </p>
    </section>

    <!-- CATALOG -->

    <main class="container">

        <h2 class="section-title">
            OUR <span>CATALOG</span>
        </h2>

        <!-- FILTERS -->

        <section class="filters">

            <div class="filter-group">
                <label for="searchInput">Search</label>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Brand, model or registration..."
                >
            </div>

            <div class="filter-group">
                <label for="brandFilter">Brand</label>
                <select id="brandFilter">
                    <option value="">All Brands</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="statusFilter">Status</label>
                <select id="statusFilter">
                    <option value="">All Status</option>
                    <option value="For Sale">For Sale</option>
                    <option value="Reserved">Reserved</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sortFilter">Sort By</label>
                <select id="sortFilter">
                    <option value="">Relevance</option>
                    <option value="asc">Lowest Price</option>
                    <option value="desc">Highest Price</option>
                </select>
            </div>

        </section>

        <!-- PRODUCT GRID -->

        <div class="products-grid" id="motasGrid">
            <p>Loading motorcycles...</p>
        </div>

    </main>

    <!-- FOOTER -->

    <footer>
        © 2026 Motocross Shop. All rights reserved.
    </footer>
    <script>
        // Variável global para guardar as motas originais vindas do servidor
        let allMotorcycles = [];

        async function loadMotorcycles() {
            const grid = document.getElementById('motasGrid');
            grid.innerHTML = '<p>Loading motorcycles...</p>';

            try {
                const response = await fetch('datatablemotas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        draw: 1,
                        start: 0,
                        length: 100,
                        'order[0][column]': 0,
                        'order[0][dir]': 'ASC',
                        'columns[0][data]': 'idmota',
                        'search[value]': ''
                    })
                });

                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }

                const result = await response.json();

                if (!result.data || result.data.length === 0) {
                    grid.innerHTML = '<p>No motorcycles found.</p>';
                    return;
                }

                // Guardar os dados recebidos na nossa variável global
                allMotorcycles = result.data;

                // Preencher o filtro de marcas dinamicamente com as marcas existentes
                populateBrandFilter(allMotorcycles);

                // Renderizar a lista inicial
                renderMotorcycles(allMotorcycles);

            } catch (error) {
                console.error('Error loading motorcycles:', error);
                grid.innerHTML = '<p>Unable to load motorcycles.</p>';
            }
        }

        // Função responsável apenas por desenhar os cartões no ecrã
        function renderMotorcycles(motorcyclesList) {
            const grid = document.getElementById('motasGrid');
            grid.innerHTML = '';

            if (motorcyclesList.length === 0) {
                grid.innerHTML = '<p>No motorcycles match your filters.</p>';
                return;
            }

            motorcyclesList.forEach(mota => {
                const image = mota.foto
                    ? `<img src="${escapeHTML(mota.foto)}" alt="${escapeHTML(mota.marca)} ${escapeHTML(mota.modelo)}">`
                    : '<div class="no-image">Image coming soon</div>';

                const card = document.createElement('div');
                card.className = 'product-card';

                card.innerHTML = `
                    <div class="product-image">${image}</div>
                    <div class="product-info">
                        <span class="product-brand">${escapeHTML(mota.marca)}</span>
                        <h3>${escapeHTML(mota.modelo)}</h3>
                        <p class="product-details">Registration: ${escapeHTML(mota.matricula)}</p>
                        <div class="product-bottom">
                            <span class="product-price">${Number(mota.preco).toFixed(2)} €</span>
                            <span class="status">${escapeHTML(mota.estado)}</span>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Preenche o <select id="brandFilter"> automaticamente com as marcas da BD
        function populateBrandFilter(motorcyclesList) {
            const brandFilter = document.getElementById('brandFilter');
            
            // Cria uma lista de marcas únicas (sem repetições)
            const brands = [...new Set(motorcyclesList.map(mota => mota.marca))].sort();
            
            // Limpa opções antigas mantendo apenas a primeira
            brandFilter.innerHTML = '<option value="">All Brands</option>';
            
            brands.forEach(brand => {
                const option = document.createElement('option');
                option.value = brand;
                option.textContent = brand;
                brandFilter.appendChild(option);
            });
        }

        // NOVA FUNÇÃO: Aplica todos os filtros (Pesquisa, Marca, Status e Ordenação) em tempo real
        function applyFilters() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const selectedBrand = document.getElementById('brandFilter').value;
            const selectedStatus = document.getElementById('statusFilter').value;
            const selectedSort = document.getElementById('sortFilter').value;

            // 1. Filtrar os dados
            let filteredList = allMotorcycles.filter(mota => {
                // Filtro de Texto (Marca, Modelo ou Matrícula)
                const matchesSearch = 
                    mota.marca.toLowerCase().includes(searchValue) || 
                    mota.modelo.toLowerCase().includes(searchValue) || 
                    mota.matricula.toLowerCase().includes(searchValue);
                
                // Filtro de Marca
                const matchesBrand = selectedBrand === "" || mota.marca === selectedBrand;
                
                // Filtro de Status (compara com mota.estado vindo do PHP)
                const matchesStatus = selectedStatus === "" || mota.estado === selectedStatus;

                return matchesSearch && matchesBrand && matchesStatus;
            });

            // 2. Ordenar os dados filtrados
            if (selectedSort === "asc") {
                filteredList.sort((a, b) => Number(a.preco) - Number(b.preco));
            } else if (selectedSort === "desc") {
                filteredList.sort((a, b) => Number(b.preco) - Number(a.preco));
            }

            // 3. Voltar a desenhar o catálogo atualizado
            renderMotorcycles(filteredList);
        }

        // Escape HTML to prevent HTML injection
        function escapeHTML(value) {
            return String(value ?? '').replace(/[&<>"']/g, char => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            })[char]);
        }

        // Ativar os ouvintes de eventos assim que o HTML estiver pronto
        document.addEventListener('DOMContentLoaded', () => {
            loadMotorcycles();

            // Escutar as mudanças em todos os inputs de filtro
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('brandFilter').addEventListener('change', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('sortFilter').addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>

