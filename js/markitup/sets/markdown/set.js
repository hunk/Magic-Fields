// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// MarkDown tags example
// http://en.wikipedia.org/wiki/Markdown
// http://daringfireball.net/projects/markdown/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
	previewParserPath:	mf_path +'markdownPreview.php',
	previewInWindow: 'width=800, height=600, resizable=yes, scrollbars=yes',
	onShiftEnter:		{keepDefault:false, openWith:'  \n'},
	onCtrlEnter:        {keepDefault:false, openWith:'\n\n'},
	markupSet: [
		{className: 'markItUpButtonH1', name:'Heading 1', key:'1', openWith:'# ', placeHolder:'Your title here...' },
		{className: 'markItUpButtonH2', name:'Heading 2', key:'2', openWith:'## ', placeHolder:'Your title here...' },
		{className: 'markItUpButtonH3', name:'Heading 3', key:'3', openWith:'### ', placeHolder:'Your title here...' },
		{className: 'markItUpButtonH4', name:'Heading 4', key:'4', openWith:'#### ', placeHolder:'Your title here...' },
		{className: 'markItUpButtonH5', name:'Heading 5', key:'5', openWith:'##### ', placeHolder:'Your title here...' },
		{className: 'markItUpButtonH6', name:'Heading 6', key:'6', openWith:'###### ', placeHolder:'Your title here...' },
		{separator:'---------------' },		
		{className: 'markItUpButtonBold', name:'Bold', key:'B', openWith:'**', closeWith:'**'},
		{className: 'markItUpButtonItalic', name:'Italic', key:'I', openWith:'_', closeWith:'_'},
		{separator:'---------------' },
		{className: 'markItUpButtonUL', name:'Bulleted List', openWith:'- ' },
		{className: 'markItUpButtonOL', name:'Numeric List', openWith:function(markItUp) {
			return markItUp.line+'. ';
		}},
		{separator:'---------------' },
		{className: 'markItUpButtonImage', name:'Picture', key:'P', replaceWith:'![[![Alternative text]!]]([![Url:!:http://]!] "[![Title]!]")'},
		{className: 'markItUpButtonLink', name:'Link', key:'L', openWith:'[', closeWith:']([![Url:!:http://]!] "[![Title]!]")', placeHolder:'Your text to link here...' },
		{separator:'---------------'},	
		{className: 'markItUpButtonQuote', name:'Quotes', openWith:'> '},
		{className: 'markItUpButtonPreformatted', name:'Preformatted Block', openWith:'~~~~~~~~~~\n', closeWith:'\n~~~~~~~~~~\n'},
		{className: 'markItUpButtonCode', name:'Code Block / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
		{separator:'---------------'},
		{name:'Preview', call:'preview', className:"preview"}
	]
}

// mIu nameSpace to avoid conflict.
miu = {
	markdownTitle: function(markItUp, char) {
		heading = '';
		n = $.trim(markItUp.selection||markItUp.placeHolder).length;
		for(i = 0; i < n; i++) {
			heading += char;
		}
		return '\n'+heading;
	}
}
