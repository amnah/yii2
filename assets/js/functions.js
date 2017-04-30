
// --------------------------------------------------------
// Page title
// --------------------------------------------------------
let pageTitleRoot = `${document.title}`

export function setPageTitleRoot(newRoot) {
    pageTitleRoot = newRoot
}

export function setPageTitle(newTitle) {

    // set document.title based on root and newTitle
    let theTitle = ''
    if (pageTitleRoot && newTitle) {
        theTitle = `${pageTitleRoot} - ${newTitle}`
    } else if (!pageTitleRoot && newTitle) {
        theTitle = newTitle
    } else if (pageTitleRoot && !newTitle) {
        theTitle = pageTitleRoot
    }
    document.title = theTitle
}
