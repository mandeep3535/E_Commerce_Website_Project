// search.js

// Function to get query parameter from URL
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }
  
  // Define your dataset with sample pages (ensure these match your actual content)
  const pages = [
    { 
      id: "1", 
      title: "Home", 
      content: "Welcome to MV Electronics. Discover the latest tech products.", 
      url: "home.html" 
    },
    { 
      id: "2", 
      title: "About Us", 
      content: "Learn about MV Electronics and our commitment to quality.", 
      url: "about.html" 
    },
    { 
      id: "3", 
      title: "Phones", 
      content: "Browse our selection of phones", 
      url: "phone.html" 
    },
    { 
        id: "4", 
        title: "Computers", 
        content: "Browse our selection of computers", 
        url: "computer.html" 
      },
      { 
        id: "5", 
        title: "SmartWatches", 
        content: "Browse our selection of smartwatches", 
        url: "smartwatch.html" 
      },
      { 
        id: "6", 
        title: "Headphones", 
        content: "Browse our selection of headphones", 
        url: "headphones.html" 
      },
      { 
        id: "7", 
        title: "Gaming", 
        content: "Browse our selection of Gaming devices", 
        url: "gaming.html" 
      },
      { 
        id: "8", 
        title: "Cart", 
        content: "Browse the cart page", 
        url: "cart.html" 
      },
      { 
        id: "9", 
        title: "Contact Us", 
        content: "Contact Us", 
        url: "contact.html" 
      },
      { 
        id: "10", 
        title: "Login", 
        content: "Login to MV Electronics", 
        url: "login.html" 
      },
      { 
        id: "11", 
        title: "Signup", 
        content: "Signup to MV Electronics", 
        url: "signup.html" 
      },
      { 
        id: "12", 
        title: "Wishlist", 
        content: "Checkout the wishlist", 
        url: "wishlist.html" 
      },
      { 
        id: "13", 
        title: "Samsung S24", 
        content: "Checkout the S24", 
        url: "product_phones_s24.html" 
      },
      { 
        id: "14", 
        title: "Iphone 12", 
        content: "Checkout the Iphone 12", 
        url: "product_phones_iphone12.htm" 
      },
      { 
        id: "15", 
        title: "Iphone 16 Pro Max", 
        content: "Checkout the Iphone 16 Pro Max", 
        url: "product_phones_iphone16.html" 
      },
      { 
        id: "16", 
        title: "Google Pixel 9", 
        content: "Checkout the Google Pixel 9", 
        url: "product_phones_googlePixel.html" 
      },
      { 
        id: "17", 
        title: "Huawei Global", 
        content: "Checkout the Huawei Global", 
        url: "product_phones_huawei.html" 
      }
  ];
  
  // Search function: Filter pages that contain the query in the title or content
  function simpleSearch(query) {
    const lowerQuery = query.toLowerCase();
    console.log("Searching for:", lowerQuery);
    const filtered = pages.filter(page =>
      page.title.toLowerCase().includes(lowerQuery) ||
      page.content.toLowerCase().includes(lowerQuery)
    );
    console.log("Filtered results:", filtered);
    return filtered;
  }
  
  //  Function to render search results on the page
  function displayResults(results, query) {
    const resultsList = document.getElementById("results");
    resultsList.innerHTML = "";
    
    // If there is no query, display default pages
    if (!query) {
      results.forEach(result => {
        const li = document.createElement("li");
        li.className = "list-group-item";
        li.innerHTML = `<a href="${result.url}"><h5>${result.title}</h5></a><p>${result.content}</p>`;
        resultsList.appendChild(li);
      });
      return;
    }
  
    // If query exists but no results were found
    if (results.length === 0) {
      resultsList.innerHTML = `<li class="list-group-item">No results found for "<strong>${query}</strong>".</li>`;
      return;
    }
    
    // Otherwise, display matching results
    results.forEach(result => {
      const li = document.createElement("li");
      li.className = "list-group-item";
      li.innerHTML = `<a href="${result.url}"><h5>${result.title}</h5></a><p>${result.content}</p>`;
      resultsList.appendChild(li);
    });
  }
  
  // Main execution: Retrieve query, perform search, and display results
  const query = getQueryParam('query') || "";
  // Log the query to check if it’s being captured properly
  console.log("Query from URL:", query);
  
  // If query is empty, display all pages; otherwise, filter based on the query
  const results = query ? simpleSearch(query) : pages;
  displayResults(results, query);
  