const colorPalette = ["000000", "ffffff", "8492A6", "EFF2F7","1FB6FF", "7E5BEF", "FF49DB", "FF4949", "13CE66", "FFC82C"];

const forePalette = document.querySelector('.fore-palette');
const backPalette = document.querySelector('.back-palette');
const toolbarButtons = document.querySelectorAll('.toolbar a');


function setPalette(selector, command) {
  for (let i = 0; i < colorPalette.length; i++) {
    const paletteItem = document.createElement("div");
    paletteItem.innerHTML = `<div data-command=${command} data-value="#${colorPalette[i]}" style="background-color: #${colorPalette[i]};" class="palette-item"></div>
      `;
    selector.append(paletteItem);
  }
}

setPalette(forePalette, "forecolor");
setPalette(backPalette, "backcolor");

toolbarButtons.forEach( button => {
  button.addEventListener("click", function(e) {
    let command = button.dataset.command;

    if (command == "h1" || command == "h2" || command == "p") {
      document.execCommand("formatBlock", false, command);
    }

    if (command == "createlink" || command == "insertimage") {
      let url = prompt("請輸入連結：", "http:\/\/");

      document.execCommand(this.dataset.command, false, url);
    } else {
      document.execCommand(this.dataset.command, false, null);
    }
  });
});


const paletteItems = Array.from(document.getElementsByClassName('palette-item'));
// const paletteItems = Array.from(document.querySlectorAll('.palette-item'));
paletteItems.forEach(item => {
  item.addEventListener("click", function() {
    let command = item.dataset.command;

    if (command == "forecolor" || command == "backcolor") {
      document.execCommand(this.dataset.command, false, this.dataset.value);
    }
  });
});

// ====================================================
// paste

const editor = document.getElementById('editor');

$(document).on('paste', '#editor', function(e) {
  // console.log(e.originalEvent.clipboardData.types)

  e.preventDefault();

  let text = '';
  let rDataText, rDataHTML;
  // let pasteBlockTags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'tbody', 'thead', 'tfoot', 'th', 'tr', 'td', 'ul', 'ol', 'li', 'blockquote', 'pre'];
  // let pasteInlineTags = ['strong', 'b', 'u', 'em', 'i', 'code', 'del', 'span', 'ins', 'samp', 'kbd', 'sup', 'sub', 'mark', 'var', 'cite', 'small'];

  const opts = {
  pasteInlineTags: ['br', 'strong', 'ins', 'code', 'del', 'span', 'samp', 'kbd', 'sup', 'sub', 'mark', 'var', 'cite', 'small', 'b', 'u', 'em', 'i'],
  pasteBlockTags: ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'table', 'tbody', 'thead', 'tfoot', 'th', 'tr', 'td', 'ul', 'ol', 'li', 'blockquote', 'pre']
}
  const reIsInline = new RegExp('^(' + opts.pasteInlineTags.join('|' ).toUpperCase() + ')$', 'i');
  const reIsBlock = new RegExp('^(' + opts.pasteBlockTags.join('|' ).toUpperCase() + ')$', 'i');

  // detect clipboardData event
  if (e.originalEvent.clipboardData) {
    let clipboardData = e.originalEvent.clipboardData;
    rDataHTML = clipboardData.getData('text/html');
    rDataText = clipboardData.getData('text/plain');
  } else if (window.clipboardData) {
    clipboardData = window.clipboardData;

    try {
      rDataHTML = clipboardData.getData('Html');
    } catch(e) {}

    rDataText = clipboardData.getData('Text');
  }

  // handle html data
  if (rDataHTML && rDataHTML.trim().length != 0) {
    console.log('data in clipboard is html');
    console.log(rDataHTML)
    // HtmlHandler(rDataHTML);
    text = $('<div />').html(rDataHTML);
    console.log(text)
  }

  function isInlineTag(tag) {
    return (typeof tag === 'undefined') ? false : reIsInline.test(tag)
  }

//   function HtmlHandler(htmlStr) {
//     // if tag has block tag, replace with <p>
//     let html;
//     let wrap = document.createElement("div");
//     wrap.innerHTML = htmlStr;

//     let allNodes = wrap.getElementsByTagName('*');
//     // console.log(allNodes.tagName)

//     Array.from(allNodes).forEach(function(element, index) {
//       console.log(isInlineTag(element.tagName))
//       console.log(element.tagName)

//       if ( !isInlineTag(element.tagName) ) {
//         text =  $('<div></div>').html(htmlStr);
//       }



//     });
//     return text;
//     console.log(text)
//   }

  // if (/text\/html/.test(e.originalEvent.clipboardData.types))
  //   text = $('<div></div>').html(rDataHTML);
  // else if (/text\/plain/.test(e.originalEvent.clipboardData.types))
  //   text = $('<div></div>').html(rDataText);


  // insert the html to editor
  document.execCommand('insertHTML', false, $(text).html());
})

