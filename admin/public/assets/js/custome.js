const {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Heading,
    Paragraph,
    List,
    Font,
    Image,
    ImageToolbar,
    ImageUpload,
    ImageCaption,
    ImageResize,
    SimpleUploadAdapter,
    Link,
    MediaEmbed // Add MediaEmbed to import
  } = CKEDITOR;
  
//   ClassicEditor
//     .create(document.querySelector('#editor'), {
//         plugins: [
//             Essentials, Bold, Italic, Heading, Paragraph, List, Font, 
//             Image, ImageToolbar, ImageUpload, ImageCaption, ImageResize, SimpleUploadAdapter,
//             Link, MediaEmbed // Add Link and MediaEmbed to the plugins list
//         ],
//         toolbar: {
//             items: [
//                 'undo', 'redo',
//                 '|', 'heading',
//                 '|', 'bold', 'italic', 'strikethrough', 'underline',
//                 '|', 'bulletedList', 'numberedList', 'todoList',
//                 '|', 'alignment',
//                 '|', 'link', 'blockQuote', 'insertTable', 'mediaEmbed', // 'link' and 'mediaEmbed' added
//                 '|', 'imageUpload',
//                 '|', 'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor'
//             ]
//         },
//         image: {
//             toolbar: [
//                 'imageStyle:inline', 'imageStyle:block', 'imageStyle:side',
//                 '|', 'toggleImageCaption', 'imageTextAlternative'
//             ],
//             resizeUnit: 'px'
//         },
//         heading: {
//             options: [
//                 { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
//                 { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
//                 { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
//                 { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
//             ]
//         },
//         mediaEmbed: {
//             previewsInData: true,
//             providers: [
//                 {
//                     name: 'twitter',
//                     url: /twitter\.com\/.*\/status\/([0-9]+)/,
//                     html: match => {
//                         const id = match[1];
//                         return `<blockquote class="twitter-tweet"><a href="https://twitter.com/x/status/${id}"></a></blockquote>`;
//                     }
//                 }
//             ]
//         },
//         simpleUpload: {
//             uploadUrl: 'http://localhost/Admin/upload.php',  // Ensure this URL matches your actual setup
//             headers: {
//                 'X-CSRF-TOKEN': 'your-csrf-token-here',  // Set CSRF token if required
//             }
//         }
//     })
//     .then(editor => {
//         console.log('Editor initialized successfully.');
//     })
//     .catch(error => {
//         console.error('Editor initialization error:', error);
//     });
  



    // tag Code 
    // Array of available tags (You can fetch this dynamically from your database using AJAX)
    const availableTags = [
        "India", "News", "Latest News", "News Update", "Delhi News", "Entertainment", "Entertainment News", "Entertainment Update", "Bollywood", "Bollywood Update", 
        "Bollywood News",  "Bollywood Actres",  "Bollywood Actor", "Trending News", "Politics News",
         "News & Politics"
    ];
    
    // Initialize Tagify
    const input = document.querySelector('#tags');
    const tagify = new Tagify(input, {
        whitelist: availableTags, // Set the available tags for suggestions
        dropdown: {
            maxItems: 10,          // Maximum number of suggestions
            enabled: 1,            // Show suggestions as soon as user types
            fuzzySearch: true,     // Enable fuzzy search for suggestions
            closeOnSelect: false   // Keep the dropdown open when selecting a tag
        }
    });
    
    // Event listener for input: fetch suggestions dynamically based on user input
    tagify.on('input', function(e) {
        const value = e.detail.value.trim(); // Trim white spaces
        if (value.length > 0) {
            // Fetch suggestions from the server based on user input
            fetch(`https://flypped.com/admin/get_tags.php?query=${encodeURIComponent(value)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Ensure the server response is an array before updating the whitelist
                    if (Array.isArray(data)) {
                        tagify.settings.whitelist = data; // Update whitelist with server response
                        tagify.dropdown.show(value);      // Show the suggestions dropdown
                    } else {
                        console.error('Invalid data format received from the server.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching tags:', error);
                });
        }
    });
    
    // Re-enable suggestions each time the tag input is focused after adding a tag
    tagify.on('add', function(e) {
        input.focus(); // Safely refocus the input field directly
    });
    