
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

// --------------------------------------------------------
// Local storage
// --------------------------------------------------------
export function setLocalStorage(key, value) {
    localStorage.setItem(key, JSON.stringify(value))
}

export function removeLocalStorage(key) {
    localStorage.removeItem(key)
}

export function getLocalStorage(key, defaultValue = null) {
    let value = localStorage.getItem(key)
    try {
        value = JSON.parse(value)
    } catch(e) {
        value = defaultValue
    }
    return value
}