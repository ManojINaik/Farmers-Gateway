<?php
include ('fsession.php');
ini_set('memory_limit', '-1');

if(!isset($_SESSION['farmer_login_user'])){
header("location: ../index.php");} // Redirecting To Home Page
$query4 = "SELECT * from farmerlogin where email='$user_check'";
              $ses_sq4 = mysqli_query($conn, $query4);
              $row4 = mysqli_fetch_assoc($ses_sq4);
              $para1 = $row4['farmer_id'];
              $para2 = $row4['farmer_name'];
?>
<!DOCTYPE html>
<html>
<?php require ('fheader.php');  ?>

<style>
.news-header {
    background: linear-gradient(150deg, #24a47f 0%, #006064 100%);
    padding: 4rem 0;
    margin-bottom: 2rem;
    color: white;
    text-align: center;
}

.news-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
}

.news-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
}

.news-card {
    height: 100%;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    border-radius: 15px;
    overflow: hidden;
    background: white;
}

.news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.news-card img {
    height: 200px;
    object-fit: cover;
    border-bottom: 1px solid #eee;
}

.news-card .card-body {
    padding: 1.5rem;
}

.news-card .card-title {
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.4;
    margin-bottom: 1rem;
    color: #2c3e50;
}

.news-card .card-text {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.6;
}

.news-card .publish-date {
    color: #95a5a6;
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.read-more-btn {
    background: linear-gradient(45deg, #24a47f, #006064);
    border: none;
    border-radius: 25px;
    padding: 0.5rem 1.5rem;
    color: white;
    font-weight: 500;
    transition: all 0.3s ease;
}

.read-more-btn:hover {
    background: linear-gradient(45deg, #006064, #24a47f);
    transform: translateY(-1px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    color: white;
}

.loading-spinner {
    margin: 2rem auto;
    text-align: center;
}

.error-message {
    background: #fff3f3;
    color: #dc3545;
    padding: 1rem;
    border-radius: 10px;
    text-align: center;
    margin: 2rem auto;
    max-width: 500px;
}

#news-container {
    min-height: 400px;
}

.news-filters {
    background: white;
    padding: 1rem;
    border-radius: 10px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.filter-btn {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 0.5rem 1rem;
    margin: 0.25rem;
    color: #495057;
    transition: all 0.3s ease;
}

.filter-btn:hover, .filter-btn.active {
    background: #24a47f;
    color: white;
    border-color: #24a47f;
}

@media (max-width: 768px) {
    .news-header {
        padding: 2rem 0;
    }
    
    .news-title {
        font-size: 2rem;
    }
    
    .news-card {
        margin-bottom: 1.5rem;
    }
}
</style>
<head>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</head>

<body class="bg-light" id="top">
  
<script>
let currentFilter = 'all';

function fetchNews(filter = 'all') {
    const newsContainer = document.getElementById('news-container');
    currentFilter = filter;
    
    // Show loading spinner
    newsContainer.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner-grow text-success" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading ${filter} news...</p>
        </div>
    `;

    // Construct query based on filter
    let query = '';
    switch(filter) {
        case 'farming-tips':
            query = 'farming+tips+agriculture+india';
            break;
        case 'market-prices':
            query = 'agriculture+market+prices+india+crops';
            break;
        case 'government-schemes':
            query = 'agriculture+government+schemes+india+farmers';
            break;
        case 'technology':
            query = 'agriculture+technology+innovation+india+farming';
            break;
        default:
            query = 'farmers+agriculture+india';
    }

    const endpoint = `https://newsapi.org/v2/everything?q=${query}&sortBy=popularity&apiKey=3ef54ba6cece40a88829bd4720c6c728`;
    
    fetch(endpoint)
    .then(response => {
        if (!response.ok) {
            throw new Error("Network response was not ok");
        }
        return response.json();
    })
    .then(data => {
        newsContainer.innerHTML = ''; // Clear loading spinner
        
        if (data.articles.length === 0) {
            newsContainer.innerHTML = `
                <div class="col-12">
                    <div class="error-message">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h4>No News Found</h4>
                        <p class="mb-0">No articles found for this category. Please try another filter.</p>
                    </div>
                </div>
            `;
            return;
        }
        
        data.articles.forEach(article => {
            const articleDiv = document.createElement('div');
            articleDiv.className = 'col-lg-4 col-md-6 mb-4';
            
            // Format the date
            const publishDate = new Date(article.publishedAt);
            const formattedDate = publishDate.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Truncate description to 150 characters
            const description = article.description || '';
            const truncatedDesc = description.length > 150 ? 
                description.substring(0, 150) + '...' : description;
            
            articleDiv.innerHTML = `
                <div class="news-card card h-100">
                    <img src="${article.urlToImage || 'https://via.placeholder.com/300x200?text=Agriculture+News'}" 
                         class="card-img-top" 
                         alt="${article.title}"
                         onerror="this.src='https://via.placeholder.com/300x200?text=Agriculture+News'">
                    <div class="card-body d-flex flex-column">
                        <div class="publish-date">
                            <i class="fas fa-calendar-alt mr-2"></i>${formattedDate}
                        </div>
                        <h5 class="card-title">${article.title}</h5>
                        <p class="card-text flex-grow-1">${truncatedDesc}</p>
                        <a href="${article.url}" target="_blank" class="read-more-btn btn mt-3">
                            Read Full Article <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            `;
            newsContainer.appendChild(articleDiv);
        });
    })
    .catch(error => {
        console.error("There was a problem fetching data from the API:", error);
        newsContainer.innerHTML = `
            <div class="col-12">
                <div class="error-message">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <h4>Oops! Something went wrong</h4>
                    <p class="mb-0">We're having trouble loading the news. Please try again later.</p>
                    <button class="btn btn-outline-danger mt-3" onclick="fetchNews('${currentFilter}')">
                        <i class="fas fa-sync-alt mr-2"></i>Try Again
                    </button>
                </div>
            </div>
        `;
    });
}

// Initialize news feed
window.addEventListener("load", () => fetchNews('all'));
</script> 

<?php include ('fnav.php');  ?>

<div class="news-header">
    <div class="container">
        <h1 class="news-title">Agricultural News & Updates</h1>
        <p class="news-subtitle">Stay informed with the latest developments in agriculture</p>
    </div>
</div>

<div class="container py-5">
    <div class="news-filters text-center mb-4">
        <button class="filter-btn active" data-filter="all">
            <i class="fas fa-newspaper mr-2"></i>All News
        </button>
        <button class="filter-btn" data-filter="farming-tips">
            <i class="fas fa-seedling mr-2"></i>Farming Tips
        </button>
        <button class="filter-btn" data-filter="market-prices">
            <i class="fas fa-chart-line mr-2"></i>Market Prices
        </button>
        <button class="filter-btn" data-filter="government-schemes">
            <i class="fas fa-landmark mr-2"></i>Government Schemes
        </button>
        <button class="filter-btn" data-filter="technology">
            <i class="fas fa-microchip mr-2"></i>Technology
        </button>
    </div>
    
    <div class="row" id="news-container">
        <!-- News articles will be loaded here -->
    </div>
</div>

<?php require("../modern-footer.php");?>

<script>
// Filter buttons functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        this.classList.add('active');
        // Fetch news with selected filter
        fetchNews(this.dataset.filter);
    });
});
</script>
</body>
</html>
