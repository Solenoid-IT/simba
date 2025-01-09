// © Solenoid Team



if (typeof Solenoid === 'undefined') Solenoid = {};

Solenoid.ClipBoard = {};



// Returns [Promise:string]
Solenoid.ClipBoard.write = async function (text)
{
    // (Copying the text to the clipboard)
    await navigator.clipboard.writeText(text);
    
    
    
    // Returning the value
    return text;
}