
// --------------------------------------------------------
// Configuration
// --------------------------------------------------------
let config = {}

export function setConfig(newConfig) {
    config = newConfig
}

export function updateConfig(key, value) {
    config[key] = value
}

export function getConfig(name, defaultValue = null) {
    return (name in config) ? config[name] : defaultValue
}

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
