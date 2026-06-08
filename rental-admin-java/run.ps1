# Script to compile and run the Java Application
$srcDir = "src"
$outDir = "bin"
$libDir = "lib\mysql-connector-j-8.3.0.jar;lib\jbcrypt-0.4.jar"
$mainClass = "drivora.Main"

# Create bin directory if it doesn't exist
if (!(Test-Path -Path $outDir)) {
    New-Item -ItemType Directory -Path $outDir | Out-Null
}

Write-Host "Compiling Java sources..." -ForegroundColor Cyan
javac -encoding UTF-8 -d $outDir -cp $libDir (Get-ChildItem -Path $srcDir -Recurse -Filter *.java | Select-Object -ExpandProperty FullName)

if ($LASTEXITCODE -eq 0) {
    Write-Host "Compilation successful. Running application..." -ForegroundColor Green
    java -cp "$outDir;$libDir" $mainClass
} else {
    Write-Host "Compilation failed." -ForegroundColor Red
}
